<?php
// Copyright (C) <2015>  <it-novum GmbH>
//
// This file is dual licensed
//
// 1.
//	This program is free software: you can redistribute it and/or modify
//	it under the terms of the GNU General Public License as published by
//	the Free Software Foundation, version 3 of the License.
//
//	This program is distributed in the hope that it will be useful,
//	but WITHOUT ANY WARRANTY; without even the implied warranty of
//	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//	GNU General Public License for more details.
//
//	You should have received a copy of the GNU General Public License
//	along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

// 2.
//	If you purchased an openITCOCKPIT Enterprise Edition you can use this file
//	under the terms of the openITCOCKPIT Enterprise Edition license agreement.
//	License agreement and license key will be shipped with the order
//	confirmation.

?>
<ol class="breadcrumb page-breadcrumb">
    <li class="breadcrumb-item">
        <a ui-sref="DashboardsIndex">
            <i class="fa fa-home"></i> <?php echo __('Home'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <a ui-sref="DashboardAllocation">
            <i class="fa fa-table"></i> <?php echo __('Dashboard Allocation'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <i class="fa fa-table"></i> <?php echo __('Allocate Dashboard'); ?>
    </li>
</ol>

<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    <span><?= __('Create Dashboard Allocation') ?>:</span>
                    <span class="fw-300">
                        {{dashboard.name}}
                    </span>
                </h2>
                <div class="panel-toolbar">
                    <?php if ($this->Acl->hasPermission('allocationManager', 'dashboards')): ?>
                        <a back-button href="javascript:void(0);" fallback-state='DashboardAllocation'
                           class="btn btn-default btn-xs mr-1 shadow-0">
                            <i class="fas fa-long-arrow-alt-left"></i> <?php echo __('Back'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form ng-submit="saveAllocation();" class="form-horizontal">

                        <!-- Select Container -->
                        <div class="form-group required" ng-class="{'has-error': errors.containers}">
                            <label class="control-label" for="UserContainers">
                                <?php echo __('Container'); ?>
                            </label>
                            <select
                                id="UserContainers"
                                data-placeholder="<?php echo __('Please choose'); ?>"
                                class="form-control"
                                chosen="containers"
                                ng-options="container.key as container.value for container in containers"
                                ng-model="post.DashboardAllocation.container_id">
                            </select>
                            <div ng-repeat="error in errors.containers">
                                <div class="help-block text-danger">{{ error }}</div>
                            </div>
                        </div>

                        <!-- Select Dashboard Tab to Share -->
                        <div class="form-group required" ng-class="{'has-error': errors.dashboard_tab_id}">
                            <label class="control-label" for="DashboardTab">
                                <?php echo __('Dashboard to allocate'); ?>
                            </label>
                            <select
                                id="DashboardTab"
                                data-placeholder="<?php echo __('Please choose'); ?>"
                                class="form-control"
                                chosen="dashboard_tabs"
                                ng-options="dashboard_tab.key as dashboard_tab.value for dashboard_tab in dashboard_tabs"
                                ng-model="post.DashboardAllocation.dashboard_tab_id">
                            </select>
                            <div ng-repeat="error in errors.dashboard_tab_id">
                                <div class="help-block text-danger">{{ error }}</div>
                            </div>
                        </div>

                        <!-- Select Users -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group margin-top-20 padding-bottom-10">
                                    <label class="control-label">
                                        <?php echo __('Allocated Users'); ?>
                                    </label>
                                    <select
                                        data-placeholder="<?php echo __('Please choose'); ?>"
                                        class="form-control"
                                        chosen="users"
                                        ng-options="user.key as user.value for user in users"
                                        ng-model="post.DashboardAllocation.usergroups_to_dashboard_tab_allocations._ids"
                                        multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Select Roles -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group margin-top-20 padding-bottom-10">
                                    <label class="control-label">
                                        <?php echo __('Allocated Roles'); ?>
                                    </label>
                                    <select
                                        data-placeholder="<?php echo __('Please choose'); ?>"
                                        class="form-control"
                                        chosen="usergroups"
                                        ng-options="usergroup.id as usergroup.name for usergroup in usergroups"
                                        ng-model="post.DashboardAllocation.users_to_dashboard_tab_allocations._ids"
                                        multiple="multiple">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- pinDashboard -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group margin-top-20 padding-bottom-10">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               id="pinDashboard"
                                               ng-model="post.DashboardAllocation.pinned">
                                        <label class="custom-control-label" for="pinDashboard">
                                            <?php echo __('Pin Dashboard'); ?>
                                        </label>
                                        <div
                                            class="help-block">
                                            <?php echo __('If enabled, this dashboard will be pinned at the leftmost tab.'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card margin-top-10">
                            <div class="card-body">
                                <div class="float-right">
                                    <button class="btn btn-primary"
                                            type="submit"><?php echo __('Create Allocation'); ?></button>
                                    <a back-button href="javascript:void(0);" fallback-state='DashboardAllocation'
                                       class="btn btn-default"><?php echo __('Cancel'); ?></a>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
