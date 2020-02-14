angular.module('openITCOCKPIT')
    .controller('HostsIndexController', function($scope, $http, $rootScope, $httpParamSerializer, SortService, MassChangeService, QueryStringService, $stateParams){
        $rootScope.lastObjectName = null;

        SortService.setSort(QueryStringService.getStateValue($stateParams, 'sort', 'Hoststatus.current_state'));
        SortService.setDirection(QueryStringService.getStateValue($stateParams, 'direction', 'desc'));
        $scope.currentPage = 1;

        $scope.useScroll = true;

        filterHostname = QueryStringService.getStateValue($stateParams, 'hostname');

        /*** Filter Settings ***/
        var defaultFilter = function(){
            $scope.filter = {
                Hoststatus: {
                    current_state: QueryStringService.hoststate($stateParams),
                    acknowledged: QueryStringService.getStateValue($stateParams,'has_been_acknowledged', false) == '1',
                    not_acknowledged: QueryStringService.getStateValue($stateParams,'has_not_been_acknowledged', false) == '1',
                    in_downtime: QueryStringService.getStateValue($stateParams,'in_downtime', false) == '1',
                    not_in_downtime: QueryStringService.getStateValue($stateParams,'not_in_downtime', false) == '1',
                    output: ''
                },
                Host: {
                    id: QueryStringService.getStateValue($stateParams, 'id', []),
                    name: (filterHostname) ? filterHostname : '',
                    description: '',
                    keywords: '',
                    not_keywords: '',
                    address: QueryStringService.getValue('filter[Hosts.address]', ''),
                    satellite_id: []
                }
            };
        };
        /*** Filter end ***/
        $scope.massChange = {};
        $scope.selectedElements = 0;
        $scope.deleteUrl = '/hosts/delete/';
        $scope.deactivateUrl = '/hosts/deactivate/';

        $scope.init = true;
        $scope.showFilter = false;


        $scope.load = function(){
            lastHostUuid = null;
            var hasBeenAcknowledged = '';
            var inDowntime = '';
            if($scope.filter.Hoststatus.acknowledged ^ $scope.filter.Hoststatus.not_acknowledged){
                hasBeenAcknowledged = $scope.filter.Hoststatus.acknowledged === true;
            }
            if($scope.filter.Hoststatus.in_downtime ^ $scope.filter.Hoststatus.not_in_downtime){
                inDowntime = $scope.filter.Hoststatus.in_downtime === true;
            }


            var params = {
                'angular': true,
                'scroll': $scope.useScroll,
                'sort': SortService.getSort(),
                'page': $scope.currentPage,
                'direction': SortService.getDirection(),
                'filter[Hosts.id][]': $scope.filter.Host.id,
                'filter[Hosts.name]': $scope.filter.Host.name,
                'filter[Hosts.description]': $scope.filter.Host.description,
                'filter[Hoststatus.output]': $scope.filter.Hoststatus.output,
                'filter[Hoststatus.current_state][]': $rootScope.currentStateForApi($scope.filter.Hoststatus.current_state),
                'filter[Hosts.keywords][]': $scope.filter.Host.keywords.split(','),
                'filter[Hosts.not_keywords][]': $scope.filter.Host.not_keywords.split(','),
                'filter[Hoststatus.problem_has_been_acknowledged]': hasBeenAcknowledged,
                'filter[Hoststatus.scheduled_downtime_depth]': inDowntime,
                'filter[Hosts.address]': $scope.filter.Host.address,
                'filter[Hosts.satellite_id][]': $scope.filter.Host.satellite_id
            };
            if(QueryStringService.getStateValue($stateParams, 'BrowserContainerId') !== null){
                params['BrowserContainerId'] = QueryStringService.getStateValue($stateParams, 'BrowserContainerId');
            }

            $http.get("/hosts/index.json", {
                params: params
            }).then(function(result){
                $scope.hosts = result.data.all_hosts;
                $scope.paging = result.data.paging;
                $scope.scroll = result.data.scroll;
                $scope.init = false;
            });
        };

        $scope.triggerFilter = function(){
            $scope.showFilter = !$scope.showFilter === true;
        };

        $scope.resetFilter = function(){
            defaultFilter();
            $scope.undoSelection();
        };

        $scope.selectAll = function(){
            if($scope.hosts){
                for(var key in $scope.hosts){
                    if($scope.hosts[key].Host.allow_edit){
                        var id = $scope.hosts[key].Host.id;
                        $scope.massChange[id] = true;
                    }
                }
            }
        };

        $scope.undoSelection = function(){
            MassChangeService.clearSelection();
            $scope.massChange = MassChangeService.getSelected();
            $scope.selectedElements = MassChangeService.getCount();
        };

        $scope.getObjectForDelete = function(host){
            var object = {};
            object[host.Host.id] = host.Host.hostname;
            return object;
        };

        $scope.getObjectsForDelete = function(){
            var objects = {};
            var selectedObjects = MassChangeService.getSelected();
            for(var key in $scope.hosts){
                for(var id in selectedObjects){
                    if(id == $scope.hosts[key].Host.id){
                        objects[id] = $scope.hosts[key].Host.hostname;
                    }
                }
            }
            return objects;
        };

        $scope.getObjectsForExternalCommand = function(){
            var objects = {};
            var selectedObjects = MassChangeService.getSelected();
            for(var key in $scope.hosts){
                for(var id in selectedObjects){
                    if(id == $scope.hosts[key].Host.id){
                        objects[id] = $scope.hosts[key];
                    }

                }
            }
            //console.log(objects);
            return objects;
        };

        $scope.linkForCopy = function(){
            var ids = Object.keys(MassChangeService.getSelected());
            return ids.join(',');
        };

        $scope.linkForEditDetails = function(){
            var ids = Object.keys(MassChangeService.getSelected());
            return ids.join(',');
        };

        var buildUrl = function(baseUrl){
            var ids = Object.keys(MassChangeService.getSelected());
            return baseUrl + ids.join('/');
        };

        $scope.linkForPdf = function(){

            var baseUrl = '/hosts/listToPdf.pdf?';

            var hasBeenAcknowledged = '';
            var inDowntime = '';
            if($scope.filter.Hoststatus.acknowledged ^ $scope.filter.Hoststatus.not_acknowledged){
                hasBeenAcknowledged = $scope.filter.Hoststatus.acknowledged === true;
            }
            if($scope.filter.Hoststatus.in_downtime ^ $scope.filter.Hoststatus.not_in_downtime){
                inDowntime = $scope.filter.Hoststatus.in_downtime === true;
            }

            var params = {
                'angular': true,
                'sort': SortService.getSort(),
                'page': $scope.currentPage,
                'direction': SortService.getDirection(),
                'filter[Hosts.name]': $scope.filter.Host.name,
                'filter[Hosts.description]': $scope.filter.Host.description,
                'filter[Hoststatus.output]': $scope.filter.Hoststatus.output,
                'filter[Hoststatus.current_state][]': $rootScope.currentStateForApi($scope.filter.Hoststatus.current_state),
                'filter[Hosts.keywords][]': $scope.filter.Host.keywords.split(','),
                'filter[Hosts.not_keywords][]': $scope.filter.Host.not_keywords.split(','),
                'filter[Hoststatus.problem_has_been_acknowledged]': hasBeenAcknowledged,
                'filter[Hoststatus.scheduled_downtime_depth]': inDowntime,
                'filter[Hosts.address]': $scope.filter.Host.address,
                'filter[Hosts.satellite_id][]': $scope.filter.Host.satellite_id
            };
            if(QueryStringService.hasValue('BrowserContainerId')){
                params['BrowserContainerId'] = QueryStringService.getValue('BrowserContainerId');
            }

            return baseUrl + $httpParamSerializer(params);
        };

        $scope.problemsOnly = function(){
            defaultFilter();
            $scope.filter.Hoststatus.not_in_downtime = true;
            $scope.filter.Hoststatus.not_acknowledged = true;
            $scope.filter.Hoststatus.current_state = {
                up: false,
                down: true,
                unreachable: true
            };
            SortService.setSort('Hoststatus.last_state_change');
            SortService.setDirection('desc');
        };

        $scope.changepage = function(page){
            $scope.undoSelection();
            if(page !== $scope.currentPage){
                $scope.currentPage = page;
                $scope.load();
            }
        };

        $scope.changeMode = function(val){
            $scope.useScroll = val;
            $scope.load();
        };

        //Fire on page load
        defaultFilter();
        SortService.setCallback($scope.load);

        jQuery(function(){
            $("input[data-role=tagsinput]").tagsinput();
        });

        $scope.$watch('filter', function(){
            $scope.currentPage = 1;
            $scope.undoSelection();
            $scope.load();
        }, true);


        $scope.$watch('massChange', function(){
            MassChangeService.setSelected($scope.massChange);
            $scope.selectedElements = MassChangeService.getCount();
        }, true);

    });
