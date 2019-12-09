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
        <a ui-sref="ServicesIndex">
            <i class="fa fa-cog"></i> <?php echo __('Services'); ?>
        </a>
    </li>
    <li class="breadcrumb-item">
        <i class="fa fa-plus"></i> <?php echo __('Edit'); ?>
    </li>
</ol>


<div class="row">
    <div class="col-xl-12">
        <div id="panel-1" class="panel">
            <div class="panel-hdr">
                <h2>
                    <?php echo __('Services'); ?>
                    <span class="fw-300"><i>
                            <?php echo __('Edit service:'); ?>
                            {{post.Service.name}}
                        </i>
                    </span>
                </h2>
                <div class="panel-toolbar">
                    <div class="text-muted cursor-default d-none d-sm-none d-md-none d-lg-block margin-right-10">
                        UUID: {{post.Service.uuid}}
                    </div>
                    <?php if ($this->Acl->hasPermission('index', 'services')): ?>
                        <a back-button fallback-state='ServicesIndex' class="btn btn-default btn-xs mr-1 shadow-0">
                            <i class="glyphicon glyphicon-white glyphicon-arrow-left"></i> <?php echo __('Back to list'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="panel-container show">
                <div class="panel-content">
                    <form ng-submit="submit();" class="form-horizontal"
                          ng-init="successMessage=
            {objectName : '<?php echo __('Service'); ?>' , message: '<?php echo __('saved successfully'); ?>'}">


                        <!-- BASIC CONFIGURATION START -->

                        <div class="card margin-bottom-10">
                            <div class="card-header">
                                <i class="fa fa-magic"></i> <?php echo __('Basic configuration'); ?>
                            </div>
                            <div class="card-body">
                                <div class="form-group required" ng-class="{'has-error': errors.host_id}">
                                    <label class="control-label" for="ServiceHosts">
                                        <?php if ($this->Acl->hasPermission('edit', 'hosts')): ?>
                                            <a ui-sref="HostsEdit({id:post.Service.host_id})">
                                                <?php echo __('Host'); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo __('Host'); ?>
                                        <?php endif; ?>
                                    </label>

                                    <!-- Services can not be moved to a different host -->
                                    <!-- For better design we use a fake select box to display the host name -->
                                    <div class="chosen-container chosen-container-single chosen-disabled"
                                         style="width: 100%;">
                                        <a class="chosen-single fake-chosen-select">
                                            <span>
                                                {{host.Host.name}}
                                            </span>
                                            <div><b></b></div>
                                        </a>
                                    </div>
                                    <div ng-repeat="error in errors.host_id">
                                        <div class="help-block text-danger">{{ error }}</div>
                                    </div>
                                </div>

                                <div class="form-group required"
                                     ng-class="{'has-error': errors.servicetemplate_id}">
                                    <label class="control-label">
                                        <?php if ($this->Acl->hasPermission('edit', 'servicetemplates')): ?>
                                            <a ui-sref="ServicetemplatesEdit({id:post.Service.servicetemplate_id})">
                                                <?php echo __('Service template'); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo __('Service template'); ?>
                                        <?php endif; ?>
                                    </label>
                                    <div ng-if="post.Service.service_type !== <?php echo MK_SERVICE; ?> && post.Service.service_type !== <?php echo OITC_AGENT_SERVICE; ?>">
                                        <select
                                            id="ServiceServicetemplateSelect"
                                            data-placeholder="<?php echo __('Please choose'); ?>"
                                            class="form-control"
                                            chosen="servicetemplates"
                                            ng-options="servicetemplate.key as servicetemplate.value for servicetemplate in servicetemplates"
                                            ng-model="post.Service.servicetemplate_id">
                                        </select>
                                    </div>
                                    <div ng-show="post.Service.host_id > 0 && post.Service.servicetemplate_id < 1"
                                         class="warning-glow">
                                        <?php echo __('Please select a service template.'); ?>
                                    </div>

                                    <div ng-if="post.Service.service_type == <?php echo MK_SERVICE; ?> || post.Service.service_type == <?php echo OITC_AGENT_SERVICE; ?>">
                                        <!-- Service template of MK_SERVICES can't be changed -->
                                        <!-- For better design we use a fake select box to display the service template name -->
                                        <div class="chosen-container chosen-container-single chosen-disabled"
                                             style="width: 100%;">
                                            <a class="chosen-single fake-chosen-select">
                                                        <span>
                                                            {{servicetemplate.Servicetemplate.template_name}}
                                                        </span>
                                                <div><b></b></div>
                                            </a>
                                        </div>
                                    </div>


                                    <div ng-repeat="error in errors.servicetemplate_id">
                                        <div class="help-block text-danger">{{ error }}</div>
                                    </div>
                                </div>

                                <div class="form-group" ng-class="{'has-error': errors.name}">
                                    <label class="control-label">
                                        <?php echo __('Service name'); ?>
                                    </label>
                                    <div class="input-group" ng-if="post.Service.service_type !== <?php echo MK_SERVICE; ?>">
                                        <input
                                            id="ServiceName"
                                            class="form-control"
                                            type="text"
                                            ng-model="post.Service.name">
                                        <template-diff ng-show="post.Service.servicetemplate_id"
                                                       value="post.Service.name"
                                                       template-value="servicetemplate.Servicetemplate.name"></template-diff>
                                    </div>

                                    <div ng-if="post.Service.service_type == <?php echo MK_SERVICE; ?>">
                                        <div class="input-group" style="width: 100%;">
                                            <input class="form-control" type="text" disabled="disabled"
                                                   readonly="readonly"
                                                   ng-model="post.Service.name">
                                        </div>
                                        <div class="help-block">
                                            <?php echo __('The name of CHECK_MK services can\'t be changed.'); ?>
                                        </div>
                                    </div>

                                    <div ng-repeat="error in errors.name">
                                        <div class="help-block text-danger">{{ name }}</div>
                                    </div>
                                </div>

                                <div ng-show="post.Service.servicetemplate_id">
                                    <div class="form-group" ng-class="{'has-error': errors.description}">
                                        <label class="control-label">
                                            <?php echo __('Description'); ?>
                                        </label>
                                        <div class="input-group">
                                            <input
                                                class="form-control"
                                                type="text"
                                                ng-model="post.Service.description">

                                            <template-diff ng-show="post.Service.servicetemplate_id"
                                                           value="post.Service.description"
                                                           template-value="servicetemplate.Servicetemplate.description">
                                            </template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.description">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>

                                    <div class="form-group" ng-class="{'has-error': errors.servicegroups}">
                                        <label class="control-label">
                                            <?php echo __('Service groups'); ?>
                                        </label>
                                        <div class="input-group">
                                            <select
                                                id="ServicegroupsSelect"
                                                data-placeholder="<?php echo __('Please choose'); ?>"
                                                class="custom-select"
                                                chosen="servicegroups"
                                                multiple
                                                ng-options="servicegroup.key as servicegroup.value for servicegroup in servicegroups"
                                                ng-model="post.Service.servicegroups._ids">
                                            </select>
                                            <template-diff ng-show="post.Service.servicetemplate_id"
                                                           value="post.Service.servicegroups._ids"
                                                           template-value="servicetemplate.Servicetemplate.servicegroups._ids"></template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.servicegroups">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>


                                    <div class="form-group" ng-class="{'has-error': errors.tags}">
                                        <label class="control-label">
                                            <?php echo __('Tags'); ?>
                                        </label>
                                        <div class="input-group">
                                            <div class="col">
                                                <div class="col">
                                                    <input class="form-control tagsinput"
                                                           data-role="tagsinput"
                                                           type="text"
                                                           ng-model="post.Service.tags"
                                                           id="ServiceTagsInput">
                                                </div>
                                            </div>
                                            <div style="margin-left: -23px;">
                                                <template-diff ng-show="post.Service.servicetemplate_id"
                                                               value="post.Service.tags"
                                                               template-value="servicetemplate.Servicetemplate.tags"
                                                               callback="restoreTemplateTags"></template-diff>
                                            </div>
                                        </div>
                                        <div ng-repeat="error in errors.tags">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                        <div class="help-block">
                                            <?php echo __('Press return to separate tags'); ?>
                                        </div>
                                    </div>


                                    <div class="form-group" ng-class="{'has-error': errors.priority}">
                                        <label class="control-label">
                                            <?php echo __('Priority'); ?>
                                        </label>
                                        <div class="col-xs-12 col-lg-2">
                                            <priority-directive priority="post.Service.priority"
                                                                callback="setPriority"></priority-directive>
                                            <template-diff-button ng-show="post.Service.servicetemplate_id"
                                                                  value="post.Service.priority"
                                                                  template-value="servicetemplate.Servicetemplate.priority">
                                            </template-diff-button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- BASIC CONFIGURATION END -->

                        <!-- CHECK CONFIGURATION START -->

                        <div class="card margin-bottom-10" ng-show="post.Service.servicetemplate_id">
                            <div class="card-header">
                                <i class="fa fa-terminal"></i> <?php echo __('Check configuration'); ?>
                            </div>
                            <div class="card-body">
                                <div class="form-group required"
                                     ng-class="{'has-error': errors.check_period_id}">
                                    <label class="control-label">
                                        <?php if ($this->Acl->hasPermission('edit', 'timeperiods')): ?>
                                            <a ui-sref="TimeperiodsEdit({id:post.Service.check_period_id})">
                                                <?php echo __('Check period'); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo __('Check period'); ?>
                                        <?php endif; ?>
                                    </label>
                                    <div class="input-group" style="width: 100%;">
                                        <select
                                            id="CheckPeriodSelect"
                                            data-placeholder="<?php echo __('Please choose'); ?>"
                                            class="form-control"
                                            chosen="checkperiods"
                                            ng-options="checkperiod.key as checkperiod.value for checkperiod in checkperiods"
                                            ng-model="post.Service.check_period_id">
                                        </select>
                                        <template-diff ng-show="post.Service.servicetemplate_id"
                                                       value="post.Service.check_period_id"
                                                       template-value="servicetemplate.Servicetemplate.check_period_id"></template-diff>
                                    </div>
                                    <div ng-repeat="error in errors.check_period_id">
                                        <div class="help-block text-danger">{{ error }}</div>
                                    </div>
                                </div>

                                <div class="form-group" ng-class="{'has-error': errors.active_checks_enabled}">
                                    <div class="custom-control custom-checkbox custom-control-down margin-bottom-10"
                                         ng-class="{'has-error': errors.active_checks_enabled}">

                                        <input type="checkbox"
                                               class="custom-control-input"
                                               ng-true-value="1"
                                               ng-false-value="0"
                                               id="active_checks_enabled"
                                               ng-model="post.Service.active_checks_enabled">
                                        <label class="custom-control-label" for="activeChecksEnabled">
                                            <?php echo __('Enable active checks'); ?>
                                        </label>
                                        <template-diff-button ng-show="post.Service.servicetemplate_id"
                                                              value="post.Service.active_checks_enabled"
                                                              template-value="servicetemplate.Servicetemplate.active_checks_enabled">
                                        </template-diff-button>
                                    </div>

                                    <div class="col col-xs-12 col-md-offset-2 help-block">
                                        <?php echo __('If disabled the check command won\'t be executed. This is useful if an external program sends state data to openITCOCKPIT.'); ?>
                                    </div>
                                </div>


                                <div class="form-group required"
                                     ng-class="{'has-error': errors.command_id}">
                                    <label class="control-label">
                                        <?php if ($this->Acl->hasPermission('edit', 'commands')): ?>
                                            <a ui-sref="CommandsEdit({id:post.Service.command_id})">
                                                <?php echo __('Check command'); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo __('Check command'); ?>
                                        <?php endif; ?>
                                    </label>
                                    <div class="input-group" style="width: 100%;">
                                        <select
                                            id="ServiceCheckCommandSelect"
                                            data-placeholder="<?php echo __('Please choose'); ?>"
                                            class="form-control"
                                            chosen="commands"
                                            ng-options="command.key as command.value for command in commands"
                                            ng-model="post.Service.command_id">
                                        </select>
                                        <template-diff ng-show="post.Service.servicetemplate_id"
                                                       value="post.Service.command_id"
                                                       template-value="servicetemplate.Servicetemplate.command_id"></template-diff>
                                    </div>
                                    <div class="help-block" ng-hide="post.Service.active_checks_enabled">
                                        <?php echo __('Due to active checking is disabled, this command will only be used as freshness check command.'); ?>
                                    </div>
                                    <div ng-repeat="error in errors.command_id">
                                        <div class="help-block text-danger">{{ error }}</div>
                                    </div>
                                </div>


                                <div class="form-group required" ng-class="{'has-error': errors.check_interval}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php echo __('Check interval'); ?>
                                    </label>
                                    <interval-input-with-differ-directive
                                        template-id="post.Service.servicetemplate_id"
                                        interval="post.Service.check_interval"
                                        template-value="servicetemplate.Servicetemplate.check_interval"></interval-input-with-differ-directive>
                                    <div class="col-xs-12 col-lg-offset-2">
                                        <div ng-repeat="error in errors.check_interval">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required" ng-class="{'has-error': errors.retry_interval}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php echo __('Retry interval'); ?>
                                    </label>
                                    <interval-input-with-differ-directive
                                        template-id="post.Service.servicetemplate_id"
                                        interval="post.Service.retry_interval"
                                        template-value="servicetemplate.Servicetemplate.retry_interval"></interval-input-with-differ-directive>

                                    <div class="col-xs-12 col-lg-offset-2">
                                        <div ng-repeat="error in errors.retry_interval">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group required"
                                     ng-class="{'has-error': errors.max_check_attempts}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php echo __('Max. number of check attempts'); ?>
                                    </label>
                                    <div class="row">
                                        <div class="col-xs-12 col-lg-6">
                                            <div class="btn-group flex-wrap">
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <button
                                                        type="button"
                                                        class="btn btn-default"
                                                        ng-click="post.Service.max_check_attempts = <?php echo h($i) ?>"
                                                        ng-class="{'active': post.Service.max_check_attempts == <?php echo h($i); ?>}">
                                                        <?php echo h($i); ?>
                                                    </button>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-lg-3">
                                            <div class="input-group" style="width: 100%;">
                                                <input
                                                    class="form-control"
                                                    type="number"
                                                    min="0"
                                                    ng-model="post.Service.max_check_attempts">
                                                <template-diff ng-show="post.Service.servicetemplate_id"
                                                               value="post.Service.max_check_attempts"
                                                               template-value="servicetemplate.Servicetemplate.max_check_attempts"></template-diff>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-lg-offset-2 col-lg-12">
                                        <div class="help-block">
                                            <?php echo __('Number of failed attempts before the service will switch into hard state.'); ?>
                                        </div>
                                        <div class="help-block">
                                            <?php echo __('Worst case time delay until notification command gets executed after state hits a non ok state: '); ?>
                                            <human-time-directive
                                                seconds="(post.Service.check_interval + (post.Service.max_check_attempts -1) * post.Service.retry_interval)"></human-time-directive>
                                        </div>
                                        <div ng-repeat="error in errors.max_check_attempts">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- CHECK CONFIGURATION END -->

                        <!-- NOTIFICATION CONFIGURATION START -->

                        <div class="card margin-bottom-10" ng-show="post.Service.servicetemplate_id">
                            <div class="card-header">
                                <i class="fa fa-wrench"></i> <?php echo __('Misc. configuration'); ?>
                            </div>
                            <div class="card-body">
                                <div class="form-group required"
                                     ng-class="{'has-error': errors.notify_period_id}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php if ($this->Acl->hasPermission('edit', 'timeperiods')): ?>
                                            <a ui-sref="TimeperiodsEdit({id:post.Service.notify_period_id})">
                                                <?php echo __('Notification period'); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo __('Notification period'); ?>
                                        <?php endif; ?>
                                    </label>
                                    <div class="input-group" style="width: 100%;">
                                        <select
                                            id="NotifyPeriodSelect"
                                            data-placeholder="<?php echo __('Please choose'); ?>"
                                            class="form-control"
                                            chosen="timeperiods"
                                            ng-options="timeperiod.key as timeperiod.value for timeperiod in timeperiods"
                                            ng-model="post.Service.notify_period_id">
                                        </select>
                                        <template-diff ng-show="post.Service.servicetemplate_id"
                                                       value="post.Service.notify_period_id"
                                                       template-value="servicetemplate.Servicetemplate.notify_period_id"></template-diff>
                                    </div>
                                    <div ng-repeat="error in errors.notify_period_id">
                                        <div class="help-block text-danger">{{ error }}</div>
                                    </div>
                                </div>

                                <div class="form-group required"
                                     ng-class="{'has-error': errors.notification_interval}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php echo __('Notification interval'); ?>
                                    </label>
                                    <interval-input-with-differ-directive
                                        template-id="post.Service.servicetemplate_id"
                                        interval="post.Service.notification_interval"
                                        template-value="servicetemplate.Servicetemplate.notification_interval"></interval-input-with-differ-directive>
                                    <div class="col-xs-12 col-lg-offset-2">
                                        <div ng-repeat="error in errors.notification_interval">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group"
                                     ng-show="data.areContactsInheritedFromHosttemplate || data.areContactsInheritedFromHost || data.areContactsInheritedFromServicetemplate">
                                    <label class="col-xs-12 col-lg-2 control-label" for="resetContacts">
                                        <?php echo __('Disable inheritance'); ?>
                                    </label>

                                    <div class="col-xs-12 col-lg-1 smart-form">
                                        <label class="checkbox no-required no-padding no-margin label-default-off">
                                            <input type="checkbox" name="checkbox"
                                                   id="resetContacts"
                                                   ng-model="data.disableInheritance">
                                            <i class="checkbox-primary"></i>
                                        </label>
                                    </div>
                                    <div class="col col-xs-12 col-md-offset-2 help-block text-info"
                                         ng-class="{'strikethrough': data.disableInheritance}">
                                        <?php echo __('Contacts and contact groups got inherited from'); ?>

                                        <span ng-class="{'bold': data.areContactsInheritedFromServicetemplate}">
                                                <?php if ($this->Acl->hasPermission('edit', 'servicetemplates')): ?>
                                                    <a ui-sref="ServicetemplatesEdit({id: post.Service.servicetemplate_id})">
                                                        <?php echo __('service template'); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php echo __('service template'); ?>
                                                <?php endif; ?>
                                            </span>

                                        <span ng-show="!data.areContactsInheritedFromServicetemplate"
                                              ng-class="{'bold': data.areContactsInheritedFromHost}">
                                                <i class="fa fa-angle-double-right"></i>
                                                <?php if ($this->Acl->hasPermission('edit', 'hosts')): ?>
                                                    <a ui-sref="HostsEdit({id: host.Host.id})">
                                                        <?php echo __('host'); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php echo __('host'); ?>
                                                <?php endif; ?>
                                        </span>

                                        <span ng-show="data.areContactsInheritedFromHosttemplate" class="bold">
                                                <i class="fa fa-angle-double-right"></i>
                                                <?php if ($this->Acl->hasPermission('edit', 'hosttemplates')): ?>
                                                    <a ui-sref="HosttemplatesEdit({id: host.Host.hosttemplate_id})">
                                                        <?php echo __('host template'); ?>
                                                    </a>
                                                <?php else: ?>
                                                    <?php echo __('host template'); ?>
                                                <?php endif; ?>
                                        </span>
                                        .
                                    </div>
                                </div>


                                <div id="ContactBlocker">
                                    <div class="form-group"
                                         ng-class="{'has-error': errors.contacts}">
                                        <label class="col-xs-12 col-lg-2 control-label">
                                            <?php echo __('Contacts'); ?>
                                        </label>
                                        <div class="input-group" style="width: 100%">
                                            <select
                                                id="ContactsPeriodSelect"
                                                data-placeholder="<?php echo __('Please choose'); ?>"
                                                class="form-control"
                                                chosen="contacts"
                                                multiple
                                                ng-options="contact.key as contact.value for contact in contacts"
                                                ng-model="post.Service.contacts._ids">
                                            </select>
                                            <template-diff ng-show="post.Service.servicetemplate_id"
                                                           value="post.Service.contacts._ids"
                                                           template-value="servicetemplate.Servicetemplate.contacts._ids"></template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.contacts">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>


                                    <div class="form-group"
                                         ng-class="{'has-error': errors.contactgroups}">
                                        <label class="col-xs-12 col-lg-2 control-label">
                                            <?php echo __('Contact groups'); ?>
                                        </label>
                                        <div class="input-group" style="width: 100%;">
                                            <select
                                                id="ContactgroupsSelect"
                                                data-placeholder="<?php echo __('Please choose'); ?>"
                                                class="form-control"
                                                chosen="contactgroups"
                                                multiple
                                                ng-options="contactgroup.key as contactgroup.value for contactgroup in contactgroups"
                                                ng-model="post.Service.contactgroups._ids">
                                            </select>
                                            <template-diff ng-show="post.Service.servicetemplate_id"
                                                           value="post.Service.contactgroups._ids"
                                                           template-value="servicetemplate.Servicetemplate.contactgroups._ids">
                                            </template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.contactgroups">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <?php
                                $serviceOptions = [
                                    [
                                        'field' => 'notify_on_recovery',
                                        'class' => 'success',
                                        'text'  => __('Recovery')
                                    ],
                                    [
                                        'field' => 'notify_on_warning',
                                        'class' => 'warning',
                                        'text'  => __('Warning')
                                    ],
                                    [
                                        'field' => 'notify_on_critical',
                                        'class' => 'danger',
                                        'text'  => __('Critical')
                                    ],
                                    [
                                        'field' => 'notify_on_unknown',
                                        'class' => 'secondary',
                                        'text'  => __('Unknown')
                                    ],
                                    [
                                        'field' => 'notify_on_flapping',
                                        'class' => 'primary',
                                        'text'  => __('Flapping')
                                    ],
                                    [
                                        'field' => 'notify_on_downtime',
                                        'class' => 'primary',
                                        'text'  => __('Downtime')
                                    ],
                                ];
                                ?>
                                <fieldset>
                                    <legend class="fs-sm"
                                            ng-class="{'has-error-no-form': errors.notify_on_recovery}">
                                        <div class="required">
                                            <label class="fs-sm">
                                                <?php echo __('Service notification options'); ?>
                                            </label>

                                            <div ng-repeat="error in errors.notify_on_recovery">
                                                <div class="text-danger">{{ error }}</div>
                                            </div>
                                        </div>
                                    </legend>
                                    <div class="row">
                                        <?php foreach ($serviceOptions as $serviceOption): ?>
                                            <div class="custom-control custom-checkbox margin-bottom-10 custom-control-right"
                                                 ng-class="{'has-error': errors.<?php echo $serviceOption['field']; ?>}">
                                                <input type="checkbox"
                                                       class="custom-control-input"
                                                       ng-true-value="1"
                                                       ng-false-value="0"
                                                       id="<?php echo $serviceOption['field']; ?>"
                                                       ng-model="post.Service.<?php echo $serviceOption['field']; ?>">
                                                <label for="<?php echo $serviceOption['field']; ?>"
                                                       class="col col-md-7 custom-control-label custom-control-label-<?php echo $serviceOption['class']; ?> padding-top-0 margin-right-10 ">
                                                        <span class="badge badge-<?php echo $serviceOption['class']; ?> notify-label-small">
                                                            <?php echo $serviceOption['text']; ?>
                                                        </span>
                                                </label>
                                                <span class="margin-left-15">
                                                    <template-diff-button ng-show="post.Service.servicetemplate_id"
                                                                          value="post.Service.<?php echo $serviceOption['field']; ?>"
                                                                          template-value="servicetemplate.Servicetemplate.<?php echo $serviceOption['field']; ?>">
                                                    </template-diff-button>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                        <!-- NOTIFICATION CONFIGURATION END -->

                        <!-- MISC. CONFIGURATION START -->

                        <div class="card margin-bottom-10" ng-show="post.Service.servicetemplate_id">
                            <div class="card-header">
                                <i class="fa fa-wrench"></i> <?php echo __('Misc. configuration'); ?>
                            </div>
                            <div class="card-body">

                                <div class="form-group" ng-class="{'has-error': errors.service_url}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php echo __('Service URL'); ?>
                                    </label>
                                    <div class="col-xs-12 col-lg-10">
                                        <div class="input-group" style="width: 100%;">
                                            <input
                                                class="form-control"
                                                placeholder="https://issues.example.org?host=$HOSTNAME$&service=$SERVICEDESC$"
                                                type="text"
                                                ng-model="post.Service.service_url">
                                            <template-diff ng-show="post.Service.servicetemplate_id"
                                                           value="post.Service.service_url"
                                                           template-value="servicetemplate.Servicetemplate.service_url"></template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.service_url">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                        <div class="help-block">
                                            <?php echo __('The macros $HOSTID$, $HOSTNAME$, $HOSTDISPLAYNAME$, $HOSTADDRESS$, $SERVICEID$, $SERVICEDESC$, $SERVICEDISPLAYNAME$ will be replaced'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" ng-class="{'has-error': errors.notes}">
                                    <label class="col-xs-12 col-lg-2 control-label">
                                        <?php echo __('Notes'); ?>
                                    </label>
                                    <div class="col-xs-12 col-lg-10">
                                        <div class="input-group" style="width: 100%;">
                                            <input
                                                class="form-control"
                                                type="text"
                                                ng-model="post.Service.notes">
                                            <template-diff ng-show="post.Service.servicetemplate_id"
                                                           value="post.Service.notes"
                                                           template-value="servicetemplate.Servicetemplate.notes"></template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.notes">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $serviceFlapOptions = [
                                    [
                                        'field' => 'flap_detection_on_ok',
                                        'class' => 'success',
                                        'text'  => __('Recovery')
                                    ],
                                    [
                                        'field' => 'flap_detection_on_warning',
                                        'class' => 'warning',
                                        'text'  => __('Warning')
                                    ],
                                    [
                                        'field' => 'flap_detection_on_critical',
                                        'class' => 'danger',
                                        'text'  => __('Critical')
                                    ],
                                    [
                                        'field' => 'flap_detection_on_unknown',
                                        'class' => 'secondary',
                                        'text'  => __('Unknown')
                                    ]
                                ];
                                ?>


                                <div class="form-group" ng-class="{'has-error': errors.flap_detection_enabled}">
                                    <div class="custom-control custom-checkbox margin-bottom-10 custom-control-down"
                                         ng-class="{'has-error': errors.flap_detection_enabled}">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               ng-true-value="1"
                                               ng-false-value="0"
                                               id="flapDetectionEnabled"
                                               ng-model="post.Service.flap_detection_enabled">
                                        <label class="custom-control-label" for="flapDetectionEnabled">
                                            <?php echo __('Flap detection enabled'); ?>
                                        </label>
                                        <template-diff-button ng-show="post.Service.servicetemplate_id"
                                                              value="post.Service.flap_detection_enabled"
                                                              template-value="servicetemplate.Servicetemplate.flap_detection_enabled">
                                        </template-diff-button>
                                    </div>
                                </div>


                                <fieldset ng-show="post.Service.flap_detection_enabled">
                                    <legend class="fs-sm"
                                            ng-class="{'has-error-no-form': errors.flap_detection_on_ok}">
                                        <div ng-class="{'required':post.Service.flap_detection_enabled}">
                                            <label class="fs-sm">
                                                <?php echo __('Flap detection options'); ?>
                                            </label>

                                            <div ng-repeat="error in errors.flap_detection_on_ok">
                                                <div class="text-danger">{{ error }}</div>
                                            </div>
                                        </div>
                                    </legend>
                                    <div class="row">
                                        <?php foreach ($serviceFlapOptions as $serviceFlapOption): ?>
                                            <div class="custom-control custom-checkbox margin-bottom-10 custom-control-right"
                                                 ng-class="{'has-error': errors.<?php echo $serviceFlapOption['field']; ?>}">
                                                <input type="checkbox" name="checkbox"
                                                       class="custom-control-input"
                                                       ng-true-value="1"
                                                       ng-false-value="0"
                                                       ng-disabled="!post.Service.flap_detection_enabled"
                                                       id="<?php echo $serviceFlapOption['field']; ?>"
                                                       ng-model="post.Service.<?php echo $serviceFlapOption['field']; ?>">
                                                <label for="<?php echo $serviceFlapOption['field']; ?>"
                                                       class="col col-md-7 custom-control-label custom-control-label-<?php echo $serviceFlapOption['class']; ?> padding-top-0 margin-right-10">
                                                        <span class="badge badge-<?php echo $serviceFlapOption['class']; ?> notify-label-small">
                                                            <?php echo $serviceFlapOption['text']; ?>
                                                        </span>
                                                </label>
                                                <span class="margin-left-15">
                                                    <template-diff-button ng-show="post.Service.servicetemplate_id"
                                                                          value="post.Service.<?php echo $serviceFlapOption['field']; ?>"
                                                                          template-value="servicetemplate.Servicetemplate.<?php echo $serviceFlapOption['field']; ?>">
                                                    </template-diff-button>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </fieldset>


                                <div class="form-group" ng-class="{'has-error': errors.is_volatile}">
                                    <div class="custom-control custom-checkbox margin-bottom-10 custom-control-down"
                                         ng-class="{'has-error': errors.is_volatile}">
                                        <input type="checkbox"
                                               class="custom-control-input"
                                               ng-true-value="1"
                                               ng-false-value="0"
                                               id="isVolatile"
                                               ng-model="post.Service.is_volatile">
                                        <label class="custom-control-label" for="isVolatile">
                                            <?php echo __('Status volatile'); ?>
                                        </label>
                                        <template-diff-button ng-show="post.Service.servicetemplate_id"
                                                              value="post.Service.is_volatile"
                                                              template-value="servicetemplate.Servicetemplate.is_volatile">
                                        </template-diff-button>
                                    </div>

                                    <div class="col-xs-12 col-lg-offset-2 col-lg-12">
                                        <div class="help-block">
                                            <?php echo __('Will force the monitoring engine to send a notification on each Non-Ok check result that will occur.'); ?>
                                            <a href="https://www.naemon.org/documentation/usersguide/volatileservices.html"
                                               target="_blank">
                                                <i class="fa fa-external-link-alt"></i>
                                                <?php echo __('Online documentation'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- MISC. CONFIGURATION END -->


                        <!-- EVENT HANDLER CONFIGURATION START -->

                        <div class="card margin-bottom-10" ng-show="post.Service.servicetemplate_id">
                            <div class="card-header">
                                <i class="fa fa-exclamation"></i> <?php echo __('Event Handler configuration'); ?>
                            </div>
                            <div class="card-body">

                                <div class="form-group required"
                                     ng-class="{'has-error': errors.eventhandler_command_id}">
                                    <label class="control-label" for="ServiceEventHandlerSelect">
                                        <?php echo __('Event Handler'); ?>
                                    </label>
                                    <div class="input-group" style="width: 100%;">
                                        <select
                                            id="ServiceEventHandlerSelect"
                                            data-placeholder="<?php echo __('Please choose'); ?>"
                                            class="form-control"
                                            chosen="eventhandlerCommands"
                                            ng-options="eventhandler.key as eventhandler.value for eventhandler in eventhandlerCommands"
                                            ng-model="post.Service.eventhandler_command_id">
                                        </select>
                                        <template-diff ng-show="post.Service.servicetemplate_id"
                                                       value="post.Service.eventhandler_command_id"
                                                       template-value="servicetemplate.Servicetemplate.eventhandler_command_id"></template-diff>
                                    </div>
                                    <div ng-repeat="error in errors.eventhandler_command_id">
                                        <div class="help-block text-danger">{{ error }}</div>
                                    </div>
                                </div>

                                <div class="form-group"
                                     ng-class="{'has-error': errors.serviceeventcommandargumentvalues}"
                                     ng-repeat="serviceeventcommandargumentvalue in post.Service.serviceeventcommandargumentvalues">
                                    <label class="col-xs-12 col-lg-offset-2 col-lg-2 control-label text-primary">
                                        {{serviceeventcommandargumentvalue.commandargument.human_name}}
                                    </label>
                                    <div class="col-xs-12 col-lg-8">
                                        <div class="input-group">
                                            <input
                                                class="form-control"
                                                type="text"
                                                ng-model="serviceeventcommandargumentvalue.value">
                                            <template-diff
                                                value="serviceeventcommandargumentvalue.value"
                                                template-value="servicetemplate.Servicetemplate.servicetemplateeventcommandargumentvalues[$index].value"></template-diff>
                                        </div>
                                        <div ng-repeat="error in errors.serviceeventcommandargumentvalue">
                                            <div class="help-block text-danger">{{ error }}</div>
                                        </div>
                                        <div class="help-block">
                                            {{serviceeventcommandargumentvalue.commandargument.name}}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group"
                                     ng-show="post.Service.eventhandler_command_id > 0 && post.Service.serviceeventcommandargumentvalues.length == 0">
                                    <div class="col-xs-12 col-lg-offset-2 text-info">
                                        <i class="fa fa-info-circle"></i>
                                        <?php echo __('This Event Handler command does not have any parameters.'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- EVENT HANDLER CONFIGURATION END -->

                        <!-- SERVICE MACRO CONFIGURATION START -->

                        <div class="card margin-bottom-10" ng-show="post.Service.servicetemplate_id">
                            <div class="card-header">
                                <i class="fa fa-dollar"></i> <?php echo __('Service macro configuration'); ?>
                            </div>
                            <div class="card-body" ng-class="{'has-error-no-form': errors.customvariables_unique}">
                                <div class="row">
                                    <div ng-repeat="error in errors.customvariables_unique">
                                        <div class=" col-xs-12 text-danger">{{ error }}</div>
                                    </div>
                                </div>

                                <div class="row" ng-repeat="customvariable in post.Service.customvariables">
                                    <macros-directive macro="customvariable"
                                                      macro-name="'<?php echo __('SERVICE'); ?>'"
                                                      index="$index"
                                                      callback="deleteMacroCallback"
                                                      errors="getMacroErrors($index)"
                                                      class="col-lg-12">
                                    </macros-directive>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-lg-12 padding-top-10 text-info"
                                         ng-show="post.Service.customvariables.length > 0">
                                        <i class="fa fa-info-circle"></i>
                                        <?php echo __('Macros in green color are inherited from the service template.'); ?>
                                    </div>
                                    <div class="col-lg-12 col-md-offset-2 padding-top-10 text-right">
                                        <button type="button" class="btn btn-success btn-sm"
                                                ng-click="addMacro()">
                                            <i class="fa fa-plus"></i>
                                            <?php echo __('Add new macro'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- SERVICE MACRO CONFIGURATION END -->
                        <div class="card margin-top-10">
                            <div class="card-body">
                                <div class="float-right">
                                    <button class="btn btn-primary" type="submit">
                                        <?php echo __('Update service'); ?>
                                    </button>
                                    <a back-button fallback-state='ServicesIndex' class="btn btn-default">
                                        <?php echo __('Cancel'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
