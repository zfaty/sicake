<?php
namespace App\Model\Table;
use App\Model\Entity\Article;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
/**
 * Articles Model
 *
 */
class CommandesDetailsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('extranet_sageweb_commandes_details');
        $this->primaryKey('id');

        $this->belongsTo('Commandes', [
            'foreignKey' => 'commande_id',
        ]);
    }

}