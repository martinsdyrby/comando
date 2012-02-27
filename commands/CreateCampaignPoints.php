<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 14, 2012
 * Time: 3:43:34 PM
 * To change this template use File | Settings | File Templates.
 */
 
class CreateCampaignPoints {

    private $user_id;
    private $hash;

    public function init($request) {

        $this->user_id = isset($request['u']) ? $request['u'] : -1;
        $this->hash = isset($request['h']) ? $request['h'] : '';
        $this->reference = isset($request['r']) ? $request['r'] : '';
    }

    public function execute() {
        $commandresult = new ComandoResult();

        if(md5($this->user_id.$this->reference.HASH_SECRET) != $this->hash) {
            $commandresult->setError('Hash mismatch.');
        } else {

            $ref = ORM::for_table(CAMPAIGN_REFERENCE_TABLE)->where('user_id', $this->user_id)->where('reference', $this->reference)->find_one();

            if($ref != null) {
                $commandresult->setError('Duplicate entry.');
            } else {

                $commandresult = Comando::execute('SaveUserPoints', array('uid' => $this->user_id, 'points' => POINTS_FOR_ACCESSING_CAMPAIGN, 'company_points' => COMPANY_POINTS_FOR_ACCESSING_CAMPAIGN));

                if($commandresult->status()) {
                    $reference = ORM::for_table(CAMPAIGN_REFERENCE_TABLE)->create();
                    $reference->user_id=$this->user_id;
                    $reference->reference=$this->reference;
                    $reference->save();
                }
            }
        }
        
        return $commandresult;
    }
}
