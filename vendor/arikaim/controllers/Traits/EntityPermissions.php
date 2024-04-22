<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Core\Controllers\Traits;

use Arikaim\Core\Db\Model;

/**
 * Entity permissions trait
*/
trait EntityPermissions 
{        
    /**
     * Get add permission message name
     *
     * @return string
     */
    protected function getAddPermissionMessage(): string
    {
        return $this->addPermissionMessage ?? 'permission.add';
    }

    /**
     * Get delete permission message name
     *
     * @return string
     */
    protected function getDeletePermissionMessage(): string
    {
        return $this->deletePermissionMessage ?? 'permission.delete';
    }

    /**
     * Add entity permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function addPermissionController($request, $response, $data)
    {
        $data->validate(true);
        
        $entityId = $data->get('entity');
        $permissions = $data->get('permissions','full');
        $permissionName = $data->get('permission_name',null);
        $type = $data->get('type',null);
        $typeId = $data->get('type_id',null);

        $model = Model::create($this->getModelClass(),$this->getExtensionName());
        if ($model == null) {
            $this->error('errors.id');
            return;
        }

        $permissionModel = Model::Permissions()->findPermission($permissionName);
        if ($permissionModel == null) {
            $this->error('Not vlaid permission');
            return;
        }

        $permission = $model->addPermission($entityId,$typeId,$permissions,$type,$permissionModel->id); 

        $this->setResponse(\is_object($permission),function() use($permission) {   
            // dispatch event
            $this->dispatch('entity.permission.add',[
                'permission' => $permission->toArray(),
                'public'     => empty($permission->relation_id),
                'type'       => $permission->relation_type,
                'related'    => ($permission->relation_type == 'user') ? $permission->related->toArray() : null,
                'entity'     => $permission->entity->toArray()
            ]);

            $this
                ->message($this->getAddPermissionMessage())
                ->field('uuid',$permission->uuid);
                
        },'errors.permission.add');
    }

    /**
     * Add user or group permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function addUserPermissionController($request, $response, $data)
    {
        $data->validate(true); 
       
        $users = Model::Users();                    
        $entityId = $data->get('entity');
        $user = \trim($data->get('user',''));
        $user = (empty($user) == true) ? $this->getUserId() : $user;
        
        $group = $data->get('group',null);
        $type = $data->get('type','user');

        $permissions = $data->get('permissions','full');
        $permissionName = $data->get('permission_name',null);

        $model = Model::create($this->getModelClass(),$this->getExtensionName());
        if (\is_object($model) == false) {
            $this->error('errors.id');
            return;
        }

        $permissionModel = (empty($permissionName) == false) ? Model::Permissions()->findPermission($permissionName) : null;
        $permissionId = (\is_object($permissionModel) == true) ? $permissionModel->id : null;
        $permission = null;

        if ($type == 'user') {
            $userFound = $users->findUser($user);
            if (\is_object($userFound) == true) {
                $permission = $model->addUserPermission($entityId,$userFound->id,$permissions,$permissionId); 
            } else {
                $this->error('errors.permission.user');
                return;
            }              
        } else {
            // add group permission
            $userGroup = Model::UserGroups()->findByColumn($group,['id','uuid','slug']);
            if (\is_object($userGroup) == true) {
                $permission = $model->addGroupPermission($entityId,$userGroup->id,$permissions,$permissionId); 
            } else {
                $this->error('errors.permission.group');
                return;
            }   
        }
        
        $this->setResponse(\is_object($permission),function() use($permission) {   
            // dispatch event
            $this->dispatch('entity.permission.add',[
                'permission' => $permission->toArray(),
                'public'     => empty($permission->relation_id),
                'type'       => $permission->relation_type,
                'related'    => ($permission->relation_type == 'user') ? $permission->related->toArray() : null,
                'entity'     => $permission->entity->toArray()
            ]);

            $this
                ->message($this->getAddPermissionMessage())
                ->field('uuid',$permission->uuid);
                
        },'errors.permission.add');
    }

    /**
     * Delete permission
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param Validator $data
     * @return Psr\Http\Message\ResponseInterface
     */
    public function deletePermissionController($request, $response, $data)
    {
        $data->validate(true); 

        $uuid = $data->get('uuid');
        $permission = Model::create($this->getModelClass(),$this->getExtensionName())->findById($uuid);
        
        if (\is_object($permission) == false) {
            $this->error('errors.permission.id');
            return;
        }         
        $result = $permission->delete();

        $this->setResponse($result,function() use($uuid,$permission) {   
            // dispatch event  
            $this->dispatch('entity.permission.delete',[
                'permission' => $permission->toArray(),
                'public'     => empty($permission->relation_id),
                'type'       => $permission->relation_type,
                'related'    => ($permission->relation_type == 'user') ? $permission->related->toArray() : null,
                'entity'     => $permission->entity->toArray()
            ]);         

            $this
                ->message($this->getDeletePermissionMessage())
                ->field('uuid',$uuid);
                
        },'errors.permission.delete');
    }
}
