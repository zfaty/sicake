<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\ForbiddenException;
use Cake\Network\Exception\NotFoundException;
use Cake\View\Exception\MissingTemplateException;
use Cake\Datasource\ConnectionManager;
/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class DepartementAdministratifController extends AppController
{

    private $MrestUrl = "http://mrest.magiconline.fr/api/parallels";
    private $MrestUSERPWD = "api:Ix6VMPsp";

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {
        // $conn = ConnectionManager::get('local');
        // $stmt = $conn->query('SELECT * FROM client LIMIT 10');
        // $stmt->execute();
        // $rows = $stmt->fetchAll('assoc');
        // $this->loadModel('Commandes');
        // $recentCommandes= $this->Commandes->find('all', [
        //     'contain' => ['CommandesDetails'],
        //     'limit' => 5,
        //     'order' => 'Commandes.n_commande_parallels DESC'
        // ]);
        // $results = $recentCommandes->toArray();
        // echo"<pre>";print_r($results);echo"</pre>";die;

        $title = 'Departement Administratif';
        
        $this->set(compact('title'));

        try {
            $this->render('index');
        } catch (MissingTemplateException $e) {
            if (Configure::read('debug')) {
                throw $e;
            }
            throw new NotFoundException();
        }
    }

    public function creeCommande()
    {
        $title = 'Departement Administratif';
        
        if($this->request->is('post')){

            $data = array();
            $data['code_representant'] = $this->request->getData('code_commercial');
            $data['commande_code_client_facture'] = $this->request->getData('code_client');
            $data['commande_code_client_livre'] = $this->request->getData('code_client');
            $data['date_creation'] =date("Y-m-d H:i:s");
            $data['commande_date_debut_abonnement'] = date("Y-m-d");

            $data['company'] = $this->request->getData('company');
            
            $data['type_commande'] = $this->request->getData('type_commande');

            $data['mode_expedition'] = '';
            $data['n_opportunite'] = '';
            $data['mode_reglement'] = '';
            $data['nom_fichier_exporte'] = '';
            $data['commentaires'] = '';
            $data['application_name'] = '';
            $data['application_id'] = '';            
            $data['extranet_user_id'] = 149;
            //echo"<pre>";print_r($data);echo"</pre>";die;
            if($data['company'] == 'MOL'){
                $data['n_commande_parallels'] = $this->request->getData('n_commande_odin');
            }
            $lastCmdId = $this->insert('extranet_sageweb_commandes',$data);

           if($lastCmdId){
                $insertedLigne = array();
                foreach ($this->request->getData('cmds') as $key => $ligne) {
                    
                    $data2 = array();
                    
                    if(isset($ligne['is_included']) || $data['company'] == 'PARALLELS'){
                        $data2['commande_id'] = $lastCmdId;
                        $data2['numero_ligne'] = $key;
                        $data2['code_article'] = $ligne['code_article'];
                        $data2['description'] = $ligne['designation'];
                        $data2['qte'] = $ligne['qte'];
                        $data2['prix_ht'] = $ligne['montant'];
                        $data2['code_stat'] = '';
                        $data2['reference'] = '';
                        $insertedLigne[] = $this->insert('extranet_sageweb_commandes_details',$data2);
                        $data['cmd'][] = $data2;
                    }


                }
                if(!empty($insertedLigne)){
                    $this->Flash->set('La commande est bien cree.', [
                        'element' => 'success',
                        'params' => [
                            'class' => 'alert alert-success'
                        ]
                    ]);
                }

            }
           // echo"<pre>";print_r($data);echo"</pre>";die;
            // echo"<pre>";print_r($insertedLigne);echo"</pre>";
        }
        $type_vente = array('New biz','Up-sell','Up-sell AetR','Down-sell','Cross-sell');
        $si_production = array('MOL'=>'Magic Online','PARALLELS'=>'Odin');
        $commercials = array(
            'BOFF'=>'BACKOFFICE',
            'AS'=>'Ahmed SAID',
            'HA'=>'Amine HADJADJ',
            'AM'=>'Anna MASSIGNAN'
        );

        $this->set(compact('title','type_vente','si_production','commercials'));

        $this->render('cree_commande');
    }

    public function listingCommandes()
    {
       $cmds = $this->getCommandesList();
       $this->set(compact('cmds'));
       $this->render('listing_commandes');
    }

    public function getCommandesList()
    {
        $this->autoRender = false;
        $conn = ConnectionManager::get('local');
        
        $q = "SELECT SQL_CACHE
                A.id as commande_id,
                DATE_FORMAT(A.date_creation,'%d/%m/%Y %H:%i') as date_creation,
                DATE_FORMAT(A.date_verification,'%d/%m/%Y %H:%i') as date_verification,
                DATE_FORMAT(A.date_modification,'%d/%m/%Y %H:%i') as date_modification,
                DATE_FORMAT(A.date_validation,'%d/%m/%Y %H:%i') as date_validation,
                DATE_FORMAT(A.date_export,'%d/%m/%Y %H:%i') as date_export,
                A.extranet_user_id ,
                A.nom_scanfax_associe,
                A.type_piece,
                A.commande_code_client_livre,
                A.commande_code_client_facture,
                A.commande_date_debut_abonnement,
                A.ecommerce_order_id,
                A.company,
                A.bon_commande_hegerys,
                A.n_commande_parallels,
                A.id_parallels, 
                A.tache_jira,
                A.application_name,
                A.application_id,
                A.num_proposition,
                A.type_commande,
                bl_liste.id as bl_id,
                bl_liste.etat_bl,
                bl_liste.id_modele ,
                
                DATE_FORMAT(A.date_facturation,'%d/%m/%Y') as date_facturation,
                DATE_FORMAT(A.date_debut_provisionning,'%d/%m/%Y') as commande_date_debut_provisionning,
                DATE_FORMAT(A.date_fin_provisionning,'%d/%m/%Y') as commande_date_fin_provisionning,
                DATE_FORMAT(A.date_envoi_bl,'%d/%m/%Y') as date_envoi_bl, 
                          
                B.id as commande_details_id,
                B.commande_id,
                B.numero_ligne,
                B.code_article,
                B.description,
                B.bon_livraison_id as blid2,
                B.qte,
                B.prix_ht,
                DATE_FORMAT(B.date_debut_provisionning,'%d/%m/%Y') as date_debut_provisionning,
                DATE_FORMAT(B.date_fin_provisionning,'%d/%m/%Y') as date_fin_provisionning,
                DATE_FORMAT(B.date_envoi_bonlivraison,'%d/%m/%Y') as date_envoi_bonlivraison,
                B.bonlivraison_filename, 
                B.date_insertion_MBT,
                extranet_basestechniques_bonslivraisondocs.id as bon_livraison_id,
                extranet_sageweb_factures_abos.id AS facture_id , 
                extranet_sageweb_factures_abos.etat_validation AS facture_validation ,         
                  
                extranet_users.login as commercial         
                   
                FROM 
                extranet_sageweb_commandes as A 
                LEFT OUTER JOIN extranet_sageweb_commandes_details as B  
                ON A.id = B.commande_id 
                
                LEFT JOIN extranet_sageweb_commandes_delais DP 
                ON DP.code_article = B.code_article
                
                LEFT JOIN bl_liste 
                ON A.id = bl_liste.numero_commande AND B.id = bl_liste.numero_commande_details 
                
                LEFT OUTER JOIN extranet_basestechniques_bonslivraisondocs 
                ON B.bonlivraison_filename = extranet_basestechniques_bonslivraisondocs.doc_name 
                
                LEFT JOIN extranet_sageweb_factures_abos 
                ON A.id = extranet_sageweb_factures_abos.commande_id 
                
                LEFT JOIN  (select login , sage_code_representant from  extranet_users where sage_code_representant != '' ) extranet_users
                ON A.code_representant  = extranet_users.sage_code_representant 
                
                LEFT JOIN L100_F_ARTICLE AA ON B.code_article = AA.AR_Ref 
                LEFT JOIN L100_F_FAMILLE FF ON AA.FA_CodeFamille = FF.FA_CodeFamille 
                ORDER BY B.id DESC LIMIT 20";

        $stmt = $conn->query($q);
        $stmt->execute();
        $rows = $stmt->fetchAll('assoc');
        $data = array();
        foreach ($rows as $key => $row) {

            $data[$row['commande_id']]['cmd']['commande_id'] = $row['commande_id'];
            $data[$row['commande_id']]['cmd']['commercial'] = $row['commercial'];
            $data[$row['commande_id']]['cmd']['company'] = $row['company'];
            $data[$row['commande_id']]['cmd']['n_commande_parallels'] = $row['n_commande_parallels'];
            $data[$row['commande_id']]['cmd']['type_commande'] = $row['type_commande'];
            $data[$row['commande_id']]['cmd']['date_creation'] = $row['date_creation'];
            $data[$row['commande_id']]['cmd']['date_verification'] = $row['date_verification'];
            $data[$row['commande_id']]['cmd']['date_modification'] = $row['date_modification'];
            $data[$row['commande_id']]['cmd']['date_validation'] = $row['date_validation'];
            
            $data[$row['commande_id']]['cmd']['commande_date_debut_provisionning'] = $row['commande_date_debut_provisionning'];
            $data[$row['commande_id']]['cmd']['commande_date_fin_provisionning'] = $row['commande_date_fin_provisionning'];

            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['numero_ligne'] = $row['numero_ligne'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['commande_details_id'] = $row['commande_details_id'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['code_article'] = $row['code_article'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['description'] = $row['description'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['qte'] = $row['qte'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['prix_ht'] = $row['prix_ht'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['date_debut_provisionning'] = $row['date_debut_provisionning'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['date_fin_provisionning'] = $row['date_fin_provisionning'];
            $data[$row['commande_id']]['cmd_articles'][$row['numero_ligne']]['date_envoi_bonlivraison'] = $row['date_envoi_bonlivraison'];
        }

        // $this->response->type('json');
        // $this->response->body(json_encode($data));
        //echo"<pre>";print_r($data);echo"</pre>";die;
        return $data;
    }

    public function getOdinOrders($ordernum)
    {
        $this->autoRender = false;


        $result = $this->mrest_curl('get_order_resources',array('ordernum'=>$ordernum));
        $orderDet = array();
        if($result){
            foreach ($result['order_details'] as $key => $row) {
                $r['is_plan'] = 0;
                if(!empty($row['orderDetDB']['resourceID'])){
                    $r['code_article'] = $row['orderDetDB']['resourceID'];
                }else{
                    if(!empty($row['orderDetDB']['planCategoryID'])){
                        $r['is_plan'] = 1; 
                        $r['code_article'] = $row['orderDetDB']['PlanID']; 
                    }else{
                        $r['code_article'] = $row['orderDet']['SKU']; 
                    }
                   
                }
                $r['resource_name'] = $row['orderDet']['Description'];
                $r['designation'] = $row['orderDet']['Description'];
                $r['qte'] = $row['orderDet']['Quantity'];
                $r['montant'] = trim(str_replace('EUR','',$row['orderDet']['UnitPrice']));
                $orderDet['data'][] = $r;
            }
        }
        //$response = array('commande_detail'=>$commande_detail , 'commande_article_list' => $commande_article_list);

        $this->response->type('json');
        $this->response->body(json_encode($orderDet));
        return $this->response;
    }

    public function mrest_curl($method,$params){

        $query = implode("/", array_values($params));
        $url = $this->MrestUrl."/".$method."/".$query;

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_USERPWD, $this->MrestUSERPWD);
        curl_setopt($handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $output = curl_exec($handle);
        $http_status = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if(curl_error($handle))
        {
            echo 'Curl error: ' . curl_error($handle);
            return false;
        }
        else
        {   
            $output = json_decode($output, true);
            if($output){
                return $output;
            }
            return false;
            
        }
    
    }

    public function addCommande($data = array())
    {
        if(!empty($data)){
            $lastInsertId = $this->insert('extranet_sageweb_commandes',$data);

        }
    }

    public function addCommandeDetails($data)
    {
        if(!empty($data)){
            $lastInsertId = $this->insert('extranet_sageweb_commandes_details',$data);
        }
    }


    public function getListOfPack()
    {
        //http://mrest.magiconline.fr/api/parallels/get_plans_by_vendor/1
        //http://mrest.magiconline.fr/api/parallels/get_resources_by_vendor/1//618/1112
        $login = 'selmouadin';
        $plans = array();
        $plans_netissime = array();
        $vendor_account_id = 1;

        $conn = ConnectionManager::get('local');
        $stmt = $conn->query("SELECT 1 FROM `extranet_users` WHERE `agence` = 'NETISSIME' AND `login` = '".$login."'");
        $stmt->execute();
        $rowCount = $stmt->rowCount();

        if ($rowCount) $vendor_account_id = 1007498;

        $plans = $this->mrest_curl('get_plans_by_vendor',compact('vendor_account_id'));

        if($vendor_account_id == 1 ){
            $vendor_account_id = 1007498;
            $plans_netissime = $this->mrest_curl('get_plans_by_vendor',compact('vendor_account_id'));
        }
        
        $plans = array_merge($plans, $plans_netissime);
        $plans_group = array();
        foreach ($plans as $key => $plan) {
            $plans_group[$plan['plan_cat_name']]['plan_cat_id'] = $plan['plan_cat_id'];
            $plans_group[$plan['plan_cat_name']]['plan_cat_name'] = $plan['plan_cat_name'];

            $p['plan_id'] = $plan['plan_id'];
            $p['plan_name'] = $plan['plan_name'];

            $plans_group[$plan['plan_cat_name']]['plans'][] = $p;
        }
        $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($plans_group));
        return $this->response;
    }

    public function getPlanResources($plan_id)
    {
        $included = 'Included';
        $resources_list = array();
        $resources_inclu = array();
        $plan = array();
        $data = array();
        $resources = $this->mrest_curl('get_plan_resources',compact('plan_id'));

        if(!empty($resources)){
            $plan['resource_id'] = $resources[0]['plan_id'];

            $plan_name = explode('fr ', $resources[0]['plan_name']);
            $plan_name = $plan_name[1];
            $plan['resource_name'] = $plan_name;
            $plan['resource_price'] = '';
            $plan['is_included'] = 0;
            $plan['is_plan'] = 1;
            foreach ($resources as $cat => $resource) {
                $resource['is_included'] = 0; 
                $resource['is_plan'] = 0;
                if($resource['resource_id']){
                    if($resource['resource_cat_name'] == $included){
                        $resource['is_included'] = 1;   
                        $resources_inclu[] = $resource;
                    }else{
                        $resources_list[] = $resource;
                    } 
                }                       
            }

            $r = array_merge($resources_inclu, $resources_list);

            array_unshift($r, $plan);
            $data['data'] = $r;
        }

        $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($data));
        return $this->response;
    }

    public function saveComment()
    {
        //echo"<pre>";print_r($_POST);echo"</pre>";die;
        $response = array();
        $login = 'selmouadin';
        if(!empty($this->request->getData('comment')) && !empty($this->request->getData('commande_id'))){
            $commande_id = $this->request->getData('commande_id');
            $date = date('Y-m-d H:i:s');
            $data = array(
                'id_commande'=> $this->request->getData('commande_id'),
                'login'=> $login,
                'commentaires'=> $this->request->getData('comment'),
                'date_insertion'=>$date
                );
            if($this->request->getData('comment_action') == 'add'){
                $comment = $this->insert('extranet_sageweb_commandes_commentaires',$data);
                $response['message'] = 'Commentaire bien ajouter';
            }elseif($this->request->getData('comment_action') == 'edit'){
                unset($data['id_commande']);
                unset($data['login']);
                unset($data['date_insertion']);
                $where = array('id'=>$this->request->getData('comment_id'));
                $comment = $this->update('extranet_sageweb_commandes_commentaires',$data,$where);
                $response['message'] = 'Commentaire bien modifier';
            }
            
            if($comment){
                $q = "SELECT * FROM extranet_sageweb_commandes_commentaires WHERE id_commande = '$commande_id' ORDER BY id DESC";
                $conn = ConnectionManager::get('local');
                $stmt = $conn->query($q);
                $stmt->execute();
                if($stmt->rowCount()){
                    $rows = $stmt->fetchAll('assoc');
                    $response['data'] = $rows;
                }
                $response['status'] = true;
                
            }else{
                $response['status'] = false;
                $response['message'] = 'Erreur';                
            }

        }else{
            $response['status'] = false;
            $response['message'] = 'Commentaire obligatoire';
        }

        
        $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($response));
        return $this->response;
    }

    public function getCmdComments($commande_id)
    {
        $response = array();
        $login = 'selmouadin';
        $q = "SELECT * FROM extranet_sageweb_commandes_commentaires WHERE id_commande = '$commande_id' ORDER BY id DESC";
        $conn = ConnectionManager::get('local');
        $stmt = $conn->query($q);
        $stmt->execute();
        
        if($stmt->rowCount()){
            $rows = $stmt->fetchAll('assoc');
            $response['status'] = true;
            $response['data'] = $rows;
        }else{
            $response['status'] = false;
            $response['message'] = 'Liste vide.';                
        }
        $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($response));
        return $this->response;
    }

    public function deleteComment($id)
    {   
        $response = array();
        if($id){
            $q = "DELETE FROM extranet_sageweb_commandes_commentaires WHERE id = ?";
            $conn = ConnectionManager::get('local');
            //$stmt = $conn->query($q);
            $d = $conn->execute($q,[$id]);
            $response['status'] = true;
            $response['message'] = 'Commentaire est bien supprimer.'; 
        }
        $this->autoRender = false;
        $this->response->type('json');
        $this->response->body(json_encode($response));
        return $this->response;
    }

    public function insert($table,$data)
    {
        if(!empty($data)){
            
            $key = implode(',', array_keys($data));
            $value = implode(',:', array_keys($data));

            $q = "INSERT INTO $table ($key) VALUES(:$value)";
            $conn = ConnectionManager::get('local');
            $stmt = $conn->prepare($q);
            foreach ($data as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->lastInsertId();
        }else{
            return false;
        }
    }   
    public function update($table,$data,$where)
    {
        if(!empty($data)){
            
            $key = implode(',', array_keys($data));
            $value = implode(',:', array_keys($data));
            $field_value = '';
            $q_where = '';
            foreach ($data as $key => $value) {
                $field_value .= $key.' = :'.$key;
            }
            foreach ($where as $key => $value) {
                $q_where .= $key.' = :'.$key;
            }
            //$q = "INSERT INTO $table ($key) VALUES(:$value)";
            $q = "UPDATE $table 
                SET $field_value
                WHERE $q_where";
            //echo $q;die;    
            $conn = ConnectionManager::get('local');
            $stmt = $conn->prepare($q);
            foreach ($data as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            foreach ($where as $key => $value) {
                $stmt->bindValue($key, $value);
            }       
            
            return $stmt->execute();
        }else{
            return false;
        }
    }    
}
