<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 20, 2012
 * Time: 12:00:39 PM
 * To change this template use File | Settings | File Templates.
 */
 
class SaveUserPoints extends AbstractValidationCommand {
    protected $uid;
    protected $points;
    protected $company_points;

    public function required() {
        return array('uid','points');
    }

    public function optional() {
        return array('company_points');
    }
    
    public function doExecute() {
        $commandResult = new ComandoResult();

        $user = ORM::for_table(USER_TABLE)->find_one($this->uid);
        if($user == null) {
            $commandResult->setError('User not found.');
        } else {
            $user->set('points', $user->points + $this->points);
            $user->save();

            $commandResult->setData('points', $user->points);


            $colleagues = ORM::for_table(USER_TABLE)->where('company_id',$user->company_id)->find_many();

            $company_score = 0;

            foreach($colleagues as $colleague) {
                $company_score += $colleague->points;
            }
            $company_score = $company_score/count($colleagues);

            $company = ORM::for_table(COMPANY_TABLE)->find_one($user->company_id);
            $company->set('points', $company_score);
            $company->save();
        }

        return $commandResult;
    }
}
