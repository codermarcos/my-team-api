<?php

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\Error\ErrorHandler;

/**
 * Users Model
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class TeamPlayersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {

        $this->belongsTo('teams')->setJoinType('INNER');

        $this->belongsTo('players')->setJoinType('INNER');

        parent::initialize($config);

        $this->primaryKey(['team_id', 'player_id']);

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmpty('player_id', 'create')
            ->allowEmpty('team_id', 'create');

        $validator
            ->requirePresence('player_id', 'create', 'This is required parameter.')
            ->notEmpty('player_id', 'player_id is required.')
            ->requirePresence('team_id', 'create', 'This is required parameter.')
            ->notEmpty('team_id', 'team_id is required.');

        return $validator;
    }

   /**
     * Modifies password before saving into database
     *
     * @param Event $event Event
     * @param EntityInterface $entity Entity
     * @param ArrayObject $options Array of options
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        if(empty($entity->created))
            $entity->modified   = date("Y-m-d H:i:s");
            
        $entity->modified   = date("Y-m-d H:i:s");

        return true;
    }

    public function getplayers($player_id, $team_id)
    {   
        $teamplayer = $this->find()->where(['player_id' => $player_id])->where(['team_id' => $team_id])->contain(['teams','players'])->toArray();

        if(!empty($teamplayer[0])) 
            return $teamplayer[0];

        return [];
        
    }

    public function getIsOwner($player_id, $team_id)
    {   
        $teamplayer = $this->find()->select(['is_owner'])->where(['player_id' => $player_id])->where(['team_id' => $team_id])->toArray();

        if(!empty($teamplayer[0])) 
            return  boolval($teamplayer[0]['is_owner']);

        return false;
        
    }

    public function deleteTeamPlayers($player_id, $team_id)
    {   
        

        if($this->find()->delete()->where(['player_id' => $player_id])->where(['team_id' => $team_id])->execute()) 
            return  'Deleted Successfully';

        return 'Error deleting';
        
    }

}

