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
use App\Model\Table\ContactgroupsTable;
use App\Model\Table\ContactsTable;
use App\Model\Table\ContainersTable;
use App\Model\Table\TimeperiodsTable;
use Cake\ORM\TableRegistry;
use itnovum\openITCOCKPIT\Core\AngularJS\Api;
use itnovum\openITCOCKPIT\Database\ScrollIndex;

/**
 * @property Hostescalation $Hostescalation
 * @property Timeperiod $Timeperiod
 * @property Host $Host
 * @property Hostgroup $Hostgroup
 * @property Contact $Contact
 * @property Contactgroup $Contactgroup
 * @property HostescalationHostMembership $HostescalationHostMembership
 * @property HostescalationHostgroupMembership $HostescalationHostgroupMembership
 * @property Container $Container
 */
class HostescalationsController extends AppController {
    public $uses = [
        'Hostescalation',
        'Timeperiod',
        'Host',
        'Hostgroup',
        'Contact',
        'Contactgroup',
        'HostescalationHostMembership',
        'HostescalationHostgroupMembership',
        'Container',
    ];
    public $layout = 'Admin.default';
    public $components = [
        'ListFilter.ListFilter',
        'RequestHandler'
    ];
    public $helpers = ['ListFilter.ListFilter'];

    public function index() {
        $this->layout = 'blank';

        $options = [
            'recursive'  => -1,
            'conditions' => [
                'Hostescalation.container_id' => $this->MY_RIGHTS,
            ],
            'contain'    => [
                'HostescalationHostMembership'      => [
                    'Host' => [
                        'fields' => [
                            'name',
                            'id',
                            'disabled'
                        ],
                    ],
                ],
                'Contact'                           => [
                    'fields' => [
                        'name', 'id',
                    ],
                ],
                'Contactgroup'                      => [
                    'Container' => [
                        'fields' => 'name',
                    ],
                    'fields'    => [
                        'id',
                    ],
                ],
                'HostescalationHostgroupMembership' => [
                    'Hostgroup' => [
                        'Container' => [
                            'fields' => 'name',
                        ],
                        'fields'    => [
                            'id',
                        ],
                    ],
                ],
                'Timeperiod'                        => [
                    'fields' => [
                        'name', 'id',
                    ],
                ],
            ],
        ];

        if (isset($this->request->query['page'])) {
            $this->Paginator->settings['page'] = $this->request->query['page'];
        }
        $query = Hash::merge($this->Paginator->settings, $options);

        if (!$this->isApiRequest()) {
            /*$this->Paginator->settings = array_merge($this->Paginator->settings, $query);
            $all_hostescalations = $this->Paginator->paginate();
            $this->set('all_hostescalations', $all_hostescalations);
            $this->set('_serialize', ['all_hostescalations']);*/
            return;
        }

        if ($this->isApiRequest() && !$this->isAngularJsRequest()) {
            if (isset($query['limit'])) {
                unset($query['limit']);
            }
            $all_hostescalations = $this->Hostescalation->find('all', $query);
            $this->set('all_hostescalations', $all_hostescalations);
            $this->set('_serialize', ['all_hostescalations']);
            return;
        } else {
            if ($this->isScrollRequest()) {
                $this->Paginator->settings = array_merge($this->Paginator->settings, $query);
                $ScrollIndex = new ScrollIndex($this->Paginator, $this);
                $all_hostescalations = $this->Hostescalation->find('all', array_merge($this->Paginator->settings, $query));
                $ScrollIndex->determineHasNextPage($all_hostescalations);
                $ScrollIndex->scroll();
            } else {
                $this->Paginator->settings = array_merge($this->Paginator->settings, $query);
                $all_hostescalations = $this->Paginator->paginate("Hostescalation", []);
            }
            //debug($this->Host->getDataSource()->getLog(false, false));
        }

        foreach ($all_hostescalations as $key => $hostescalation) {
            $all_hostescalations[$key]['Hostescalation']['allowEdit'] = $this->isWritableContainer($hostescalation['Hostescalation']['container_id']);
        }

        $this->set('all_hostescalations', $all_hostescalations);
        $toJson = ['all_hostescalations', 'paging'];
        if ($this->isScrollRequest()) {
            $toJson = ['all_hostescalations', 'scroll'];
        }

        $this->set('_serialize', $toJson);
    }

    public function view($id = null) {
        if (!$this->isApiRequest()) {
            throw new MethodNotAllowedException();

        }
        if (!$this->Hostescalation->exists($id)) {
            throw new NotFoundException(__('Invalid hostescalation'));
        }
        $hostescalation = $this->Hostescalation->find('first', [
            'conditions' => [
                'Hostescalation.id' => $id,
            ],
            'contain'    => [
                'HostescalationHostMembership'      => [
                    'Host',
                ],
                'Contact',
                'Contactgroup'                      => [
                    'Container',
                ],
                'HostescalationHostgroupMembership' => [
                    'Hostgroup',
                ],
                'Timeperiod',
            ],
        ]);
        if (!$this->allowedByContainerId($hostescalation['Hostescalation']['container_id'])) {
            $this->render403();

            return;
        }

        $this->set('hostescalation', $hostescalation);
        $this->set('_serialize', ['hostescalation']);
    }

    public function edit($id = null) {
        $this->layout = 'blank';
        if (!$this->isAngularJsRequest() && $id === null) {
            return;
        }

        if (!$this->Hostescalation->exists($id) && $id !== null) {
            throw new NotFoundException(__('Invalid hostescalation'));
        }
        $hostescalation = $this->Hostescalation->findById($id);

        if (!$this->allowedByContainerId($hostescalation['Hostescalation']['container_id'])) {
            $this->render403();

            return;
        }

        $hostescalation['Hostescalation']['id'] = intval($hostescalation['Hostescalation']['id']);
        $hostescalation['Hostescalation']['container_id'] = intval($hostescalation['Hostescalation']['container_id']);
        $hostescalation['Hostescalation']['timeperiod_id'] = intval($hostescalation['Hostescalation']['timeperiod_id']);
        $hostescalation['Hostescalation']['first_notification'] = intval($hostescalation['Hostescalation']['first_notification']);
        $hostescalation['Hostescalation']['last_notification'] = intval($hostescalation['Hostescalation']['last_notification']);
        $hostescalation['Hostescalation']['notification_interval'] = intval($hostescalation['Hostescalation']['notification_interval']);
        $hostescalation['Hostescalation']['escalate_on_recovery'] = intval($hostescalation['Hostescalation']['escalate_on_recovery']);
        $hostescalation['Hostescalation']['escalate_on_down'] = intval($hostescalation['Hostescalation']['escalate_on_down']);
        $hostescalation['Hostescalation']['escalate_on_unreachable'] = intval($hostescalation['Hostescalation']['escalate_on_unreachable']);

        /** @var $ContainersTable ContainersTable */
        $ContainersTable = TableRegistry::getTableLocator()->get('Containers');
        /** @var $TimeperiodsTable TimeperiodsTable */
        $TimeperiodsTable = TableRegistry::getTableLocator()->get('Timeperiods');
        /** @var $ContactsTable ContactsTable */
        $ContactsTable = TableRegistry::getTableLocator()->get('Contacts');
        /** @var $ContactgroupsTable ContactgroupsTable */
        $ContactgroupsTable = TableRegistry::getTableLocator()->get('Contactgroups');

        $containers = $ContainersTable->easyPath($this->MY_RIGHTS, OBJECT_HOSTESCALATION, [], $this->hasRootPrivileges);
        $containers = Api::makeItJavaScriptAble($containers);

        $containerIds = $ContainersTable->resolveChildrenOfContainerIds($hostescalation['Hostescalation']['container_id']);
        $hostgroups = Api::makeItJavaScriptAble($this->Hostgroup->hostgroupsByContainerId($containerIds, 'list', 'id'));
        $hostgroupsExcluded = $hostgroups;
        $hosts = Api::makeItJavaScriptAble($this->Host->hostsByContainerId($containerIds, 'list'));
        $hostsExcluded = $hosts;
        $timeperiods = Api::makeItJavaScriptAble($TimeperiodsTable->timeperiodsByContainerId($containerIds, 'list'));

        $contacts = Api::makeItJavaScriptAble($ContactsTable->contactsByContainerId($containerIds, 'list'));
        $contactgroups = Api::makeItJavaScriptAble($ContactgroupsTable->getContactgroupsByContainerId($containerIds, 'list', 'id'));

        //$this->Frontend->set('data_placeholder', __('Please choose'));
        //$this->Frontend->set('data_placeholder_empty', __('No entries found'));


        if ($this->request->is('post') || $this->request->is('put')) {

            if ($this->request->data('Hostescalation.container_id') > 0 && $this->request->data('Hostescalation.container_id') != $hostescalation['Hostescalation']['container_id']) {
                $containerIds = $ContainersTable->resolveChildrenOfContainerIds($this->request->data('Hostescalation.container_id'));
                $hostgroups = $this->Hostgroup->hostgroupsByContainerId($containerIds, 'list', 'id');
                $hosts = $this->Host->hostsByContainerId($containerIds, 'list');
                $timeperiods = $TimeperiodsTable->timeperiodsByContainerId($containerIds, 'list');
                $contacts = $ContactsTable->contactsByContainerId($containerIds, 'list');
                $contactgroups = $ContactgroupsTable->getContactgroupsByContainerId($containerIds, 'list', 'id');
            }

            $this->request->data['Contact']['Contact'] = $this->request->data['Hostescalation']['Contact'];
            $this->request->data['Contactgroup']['Contactgroup'] = $this->request->data['Hostescalation']['Contactgroup'];
            $_hosts = ($this->request->data['Hostescalation']['Host']) ? $this->request->data['Hostescalation']['Host'] : [];
            $_hosts_excluded = ($this->request->data('Hostescalation.Host_excluded')) ? $this->request->data['Hostescalation']['Host_excluded'] : [];
            $this->request->data['HostescalationHostMembership'] = [];
            $this->request->data['HostescalationHostMembership'] = $this->Hostescalation->parseHostMembershipData($_hosts, $_hosts_excluded);

            $_hostgroups = (is_array($this->request->data['Hostescalation']['Hostgroup'])) ? $this->request->data['Hostescalation']['Hostgroup'] : [];
            $_hostgroups_excluded = ($this->request->data('Hostescalation.Hostgroup_excluded')) ? $this->request->data['Hostescalation']['Hostgroup_excluded'] : [];
            $this->request->data['HostescalationHostgroupMembership'] = [];
            $this->request->data['HostescalationHostgroupMembership'] = $this->Hostescalation->parseHostgroupMembershipData($_hostgroups, $_hostgroups_excluded);

            $this->Hostescalation->set($this->request->data);
            if ($this->Hostescalation->validates()) {
                $this->Hostescalation->id = $id;
                $old_membership_hosts = $this->HostescalationHostMembership->find('all', [
                    'conditions' => [
                        'HostescalationHostMembership.hostescalation_id' => $id
                    ],
                ]);
                /* Delete old host associations */
                foreach ($old_membership_hosts as $old_membership_host) {
                    $this->HostescalationHostMembership->delete($old_membership_host['HostescalationHostMembership']['id']);
                }
                $old_membership_hostgroups = $this->HostescalationHostgroupMembership->find('all', [
                    'conditions' => [
                        'HostescalationHostgroupMembership.hostescalation_id' => $id
                    ],
                ]);
                /* Delete old hostgroup associations */
                foreach ($old_membership_hostgroups as $old_membership_hostgroup) {
                    $this->HostescalationHostgroupMembership->delete($old_membership_hostgroup['HostescalationHostgroupMembership']['id']);
                }
            }
            if ($this->Hostescalation->saveAll($this->request->data)) {
                $this->serializeId();
            } else {
                $this->serializeErrorMessage();
            }
            return;
        } else {
            $hostescalation['Hostescalation']['Host'] = array_map('intval', array_values(Hash::combine($hostescalation['HostescalationHostMembership'], '{n}[excluded=0].host_id', '{n}[excluded=0].host_id')));
            $hostescalation['Hostescalation']['Host_excluded'] = array_map('intval', array_values(Hash::combine($hostescalation['HostescalationHostMembership'], '{n}[excluded=1].host_id', '{n}[excluded=1].host_id')));
            $hostescalation['Hostescalation']['Hostgroup'] = array_map('intval', array_values(Hash::combine($hostescalation['HostescalationHostgroupMembership'], '{n}[excluded=0].hostgroup_id', '{n}[excluded=0].hostgroup_id')));
            $hostescalation['Hostescalation']['Hostgroup_excluded'] = array_map('intval', array_values(Hash::combine($hostescalation['HostescalationHostgroupMembership'], '{n}[excluded=1].hostgroup_id', '{n}[excluded=1].hostgroup_id')));
            $hostescalation['Hostescalation']['Contact'] = array_map('intval', array_values(Hash::extract($hostescalation['Contact'], '{n}.id')));
            $hostescalation['Hostescalation']['Contactgroup'] = array_map('intval', array_values(Hash::extract($hostescalation['Contactgroup'], '{n}.id')));
        }
        $this->request->data = Hash::merge($hostescalation, $this->request->data);

        $this->set(compact(['hostescalation', 'containers', 'hosts', 'hostsExcluded', 'hostgroups', 'hostgroupsExcluded', 'timeperiods', 'contactgroups', 'contacts']));
        $this->set('_serialize', ['hostescalation','containers', 'hosts', 'hostsExcluded', 'hostgroups', 'hostgroupsExcluded', 'timeperiods', 'contactgroups', 'contacts']);
    }

    public function add() {
        $this->layout = 'blank';
        if (!$this->isAngularJsRequest()) {
            return;
        }

        /** @var $ContainersTable ContainersTable */
        $ContainersTable = TableRegistry::getTableLocator()->get('Containers');
        /** @var $TimeperiodsTable TimeperiodsTable */
        $TimeperiodsTable = TableRegistry::getTableLocator()->get('TimeperiodsTable');
        /** @var $ContactsTable ContactsTable */
        $ContactsTable = TableRegistry::getTableLocator()->get('Contacts');
        /** @var $ContactgroupsTable ContactgroupsTable */
        $ContactgroupsTable = TableRegistry::getTableLocator()->get('Contactgroups');

        $containers = $ContainersTable->easyPath($this->MY_RIGHTS, OBJECT_HOSTESCALATION, [], $this->hasRootPrivileges);
        $containers = Api::makeItJavaScriptAble($containers);

        $hosts = [];
        $hostgroups = [];
        $timeperiods = [];
        $contactgroups = [];
        $contacts = [];

        //$this->Frontend->set('data_placeholder', __('Please choose'));
        //$this->Frontend->set('data_placeholder_empty', __('No entries found'));

        if ($this->request->is('post') || $this->request->is('put')) {
            App::uses('UUID', 'Lib');
            $this->request->data['Hostescalation']['uuid'] = UUID::v4();

            $arrayKeys = [
                'Contact',
                'Contactgroup',
                'Host',
                'Host_excluded',
                'Hostgroup',
                'Hostgroup_excluded',
            ];
            foreach ($arrayKeys as $key) {
                if (!array_key_exists($key, $this->request->data['Hostescalation'])) {
                    $this->request->data['Hostescalation'][$key] = [];
                }
            }

            $this->request->data['Contact']['Contact'] = $this->request->data['Hostescalation']['Contact'];
            $this->request->data['Contactgroup']['Contactgroup'] = $this->request->data['Hostescalation']['Contactgroup'];
            $hosts = (is_array($this->request->data['Hostescalation']['Host'])) ? $this->request->data['Hostescalation']['Host'] : [];
            $hosts_excluded = (is_array($this->request->data['Hostescalation']['Host_excluded'])) ? $this->request->data['Hostescalation']['Host_excluded'] : [];
            $this->request->data['HostescalationHostMembership'] = [];
            $this->request->data['HostescalationHostMembership'] = $this->Hostescalation->parseHostMembershipData($hosts, $hosts_excluded);

            $hostgroups = (is_array($this->request->data['Hostescalation']['Hostgroup'])) ? $this->request->data['Hostescalation']['Hostgroup'] : [];
            $hostgroups_excluded = (is_array($this->request->data['Hostescalation']['Hostgroup_excluded'])) ? $this->request->data['Hostescalation']['Hostgroup_excluded'] : [];
            $this->request->data['HostescalationHostgroupMembership'] = $this->Hostescalation->parseHostgroupMembershipData($hostgroups, $hostgroups_excluded);

            $this->Hostescalation->set($this->request->data);

            if ($this->Hostescalation->saveAll($this->request->data)) {
                $this->serializeId();
                return;
            } else {
                $this->serializeErrorMessage();
                return;
            }
        }

        $this->set(compact(['containers', 'hosts', 'hostgroups', 'timeperiods', 'contactgroups', 'contacts']));
        $this->set('_serialize', ['containers', 'hosts', 'hostgroups', 'timeperiods', 'contactgroups', 'contacts']);
    }

    public function delete($id = null) {
        if (!$this->Hostescalation->exists($id)) {
            throw new NotFoundException(__('Invalid hostescalation'));
        }
        $hostescalation = $this->Hostescalation->findById($id);
        if (!$this->allowedByContainerId($hostescalation['Hostescalation']['container_id'])) {
            $this->render403();

            return;
        }

        if ($this->Hostescalation->delete($id)) {
            $this->setFlash(__('Hostescalation deleted'));
            $this->redirect(['action' => 'index']);
        }
        $this->setFlash(__('Could not delete hostescalation'), false);
        $this->redirect(['action' => 'index']);
    }

    public function loadElementsByContainerId($containerId = null) {
        if (!$this->isApiRequest()) {
            throw new MethodNotAllowedException(__('This is only allowed via API.'));
            return;
        }

        /** @var $ContainersTable ContainersTable */
        $ContainersTable = TableRegistry::getTableLocator()->get('Containers');
        /** @var $TimeperiodsTable TimeperiodsTable */
        $TimeperiodsTable = TableRegistry::getTableLocator()->get('Timeperiods');
        /** @var $ContactsTable ContactsTable */
        $ContactsTable = TableRegistry::getTableLocator()->get('Contacts');
        /** @var $ContactgroupsTable ContactgroupsTable */
        $ContactgroupsTable = TableRegistry::getTableLocator()->get('Contactgroups');

        if (!$ContainersTable->existsById($containerId)) {
            throw new NotFoundException(__('Invalid hosttemplate'));
        }

        $containerIds = $ContainersTable->resolveChildrenOfContainerIds($containerId);

        $hostgroups = $this->Hostgroup->hostgroupsByContainerId($containerIds, 'list', 'id');
        $hostgroups = Api::makeItJavaScriptAble($hostgroups);
        $hostgroupsExcluded = $hostgroups;

        $hosts = $this->Host->hostsByContainerId($containerIds, 'list');
        $hosts = Api::makeItJavaScriptAble($hosts);
        $hostsExcluded = $hosts;

        $timeperiods = $TimeperiodsTable->timeperiodsByContainerId($containerIds, 'list');
        $timeperiods = Api::makeItJavaScriptAble($timeperiods);

        $contacts = $ContactsTable->contactsByContainerId($containerIds, 'list');
        $contacts = Api::makeItJavaScriptAble($contacts);

        $contactgroups = $ContactgroupsTable->getContactgroupsByContainerId($containerIds, 'list', 'id');
        $contactgroups = Api::makeItJavaScriptAble($contactgroups);

        $this->set(compact(['hosts', 'hostsExcluded', 'hostgroups', 'hostgroupsExcluded', 'timeperiods', 'contacts', 'contactgroups']));
        $this->set('_serialize', ['hosts', 'hostsExcluded', 'hostgroups', 'hostgroupsExcluded', 'timeperiods', 'contacts', 'contactgroups']);
    }
}
