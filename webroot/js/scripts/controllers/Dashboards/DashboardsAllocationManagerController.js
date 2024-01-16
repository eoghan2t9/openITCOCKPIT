angular.module('openITCOCKPIT')
    .controller('DashboardsAllocationManagerController', function($scope, $http, $rootScope, SortService, MassChangeService, QueryStringService, NotyService){
        //
        SortService.setSort(QueryStringService.getValue('sort', 'name'));
        SortService.setDirection(QueryStringService.getValue('direction', 'asc'));

        // I am the array of available dashboardTabs.
        $scope.dashboardTabs = [];

        // I am the array of available users.
        $scope.users = [];

        // I am the array of available usergroups.
        $scope.usergroups = [];

        // I am ...
        $scope.massChange = {};
        $scope.selectedElements = 0;
        $scope.deleteUrl = '/dashboards/deallocate/';

        // I am the object that is being transported to JSON API.
        $scope.allocation = {
            DashboardTab: {
                id: 0,
                usergroups: {
                    _ids: []
                },
                AllocatedUsers: {
                    _ids: []
                },
                flags: 0
            }
        };

        // I am the filter transport object.
        $scope.filter = {
            DashboardTab: {
                name: ''
            },
            full_name : ''
        };

        var defaultFilter = function(){
            $scope.filter = {
                DashboardTab: {
                    name: ''
                },
                full_name : ''
            };
        };

        // I will prepeare the view.
        $scope.load = function(){
            $http.get("/dashboards/allocationManager.json?angular=true", {
                params: {
                    'angular': true,
                    'scroll': $scope.useScroll,
                    'sort': SortService.getSort(),
                    'page': $scope.currentPage,
                    'direction': SortService.getDirection(),
                    'filter[DashboardTabs.name]': $scope.filter.DashboardTab.name,
                    'filter[full_name]' : $scope.filter.full_name
                }
            }).then(function(result){
                $scope.dashboardTabs = result.data.dashboardTabs;
                $scope.paging = result.data.paging;
                $scope.scroll = result.data.scroll;
            });
        }

        //
        $scope.changepage = function(page){
            if(page !== $scope.currentPage){
                $scope.currentPage = page;
                $scope.load();
            }
        };

        //
        $scope.changeMode = function(val){
            $scope.useScroll = val;
            $scope.load();
        };

        // I will load all users.
        $scope.loadUsers = function(){
            $http.get("/users/loadUsersByContainerId.json", {
                params: {
                    'angular': true,
                    'containerId': 1
                }
            }).then(function(result){
                $scope.users = result.data.users;
            });
        };

        // I will load all Usergroups.
        $scope.loadUsergroups = function(){
            $http.get("/usergroups/index.json", {
                params: {
                    'sort': 'Usergroups.name',
                    'direction': 'asc'
                }
            }).then(function(result){
                $scope.usergroups = result.data.allUsergroups;
            });
        };

        // I will soley return the complete tab object by the given tabId.
        $scope.getTab = function(tabId){
            for(var tabIndex in $scope.dashboardTabs){
                let currentTab = $scope.dashboardTabs[tabIndex];
                if(currentTab.id !== tabId){
                    continue;
                }
                return currentTab;
            }
            return null;
        }

        // I will show the Allocation Manager modal.
        $scope.manageAllocation = function(tabId){
            let myTab = $scope.getTab(tabId);
            // Fetch users and groups.
            $scope.loadUsergroups();
            $scope.loadUsers();

            // Put the stuff into the form.
            $scope.allocation.DashboardTab.id = myTab.id;
            $scope.allocation.DashboardTab.usergroups._ids = myTab.usergroups;
            $scope.allocation.DashboardTab.AllocatedUsers._ids = myTab.allocated_users;
            $scope.allocation.DashboardTab.flags = myTab.flags;

            // Show the form.
            $('#allocateDashboardModal').modal('show');
        }

        // I will store the allocation details.
        $scope.saveAllocation = function(){
            $http.post("/dashboards/allocate.json?angular=true", $scope.allocation).then(function(result){
                // Yes it worked.
                $scope.errors = {};
                genericSuccess();

                // Reload table.
                $scope.load();

                // Hide the form.
                $('#allocateDashboardModal').modal('hide');
            }, function errorCallback(result){
                $scope.errors = result.data.error;
                genericError();
            });
        }

        // If the [pinned] flag is switched, pass it to the flag int.
        $scope.$watch('isPinned', function(val){
            if(val){
                $scope.allocation.flags |= 1;
                return;
            }
            $scope.allocation.flags ^= 1;
        });

        var genericError = function(){
            new Noty({
                theme: 'metroui',
                type: 'error',
                text: 'Error while saving data',
                timeout: 3500
            }).show();
        };

        var genericSuccess = function(){
            new Noty({
                theme: 'metroui',
                type: 'success',
                text: 'Data saved successfully',
                timeout: 3500
            }).show();
        };


        $scope.getObjectForDelete = function(dashboardTab){
            var object = {};
            object[dashboardTab.id] = dashboardTab.name;
            console.log(object);
            return object;
        };

        $scope.getObjectsForDelete = function(){
            var objects = {};
            var selectedObjects = MassChangeService.getSelected();
            for(var key in $scope.users){
                for(var id in selectedObjects){
                    if(id == $scope.users[key].id){
                        if($scope.users[key].allow_edit === true){
                            objects[id] = $scope.users[key].full_name;
                        }
                    }
                }
            }
            return objects;
        };

        SortService.setCallback($scope.load);
        $scope.load();

        // Trigger filter show / Hide.
        $scope.triggerFilter = function(){
            $scope.showFilter = !$scope.showFilter === true;
        };

        // Duh...
        $scope.resetFilter = function(){
            defaultFilter();
        };


        $scope.undoSelection = function(){
            MassChangeService.clearSelection();
            $scope.massChange = MassChangeService.getSelected();
            $scope.selectedElements = MassChangeService.getCount();
        };

        $scope.selectAll = function(){
            if($scope.dashboardTabs){
                for(var key in $scope.dashboardTabs){
                    var id = $scope.dashboardTabs[key].id;
                    $scope.massChange[id] = true;
                }
            }
        };

        $scope.getObjectsForDelete = function(){
            var objects = {};
            var selectedObjects = MassChangeService.getSelected();
            for(var key in $scope.dashboardTabs){
                for(var id in selectedObjects){
                    if(id == $scope.dashboardTabs[key].id){
                        objects[id] = $scope.dashboardTabs[key].name;
                    }
                }
            }
            return objects;
        };

        $scope.$watch('filter', function(){
            $scope.currentPage = 1;
            $scope.undoSelection();
            $scope.load();
        }, true);

    });
