<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * DashboardTabAllocation Entity
 *
 * @property int $id
 * @property string $name
 * @property int $dashboard_tab_id
 * @property int $container_id
 * @property int $user_id
 * @property int $pinned
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\DashboardTab $dashboard_tab
 * @property \App\Model\Entity\Container $container
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\UsergroupsToDashboardTabAllocation[] $usergroups_to_dashboard_tab_allocations
 * @property \App\Model\Entity\UsersToDashboardTabAllocation[] $users_to_dashboard_tab_allocations
 */
class DashboardTabAllocation extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'name' => true,
        'dashboard_tab_id' => true,
        'container_id' => true,
        'user_id' => true,
        'pinned' => true,
        'created' => true,
        'modified' => true,
        'dashboard_tab' => true,
        'container' => true,
        'user' => true,
        'usergroups_to_dashboard_tab_allocations' => true,
        'users_to_dashboard_tab_allocations' => true,
    ];
}
