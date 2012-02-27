<?php
/**
 * Created by PhpStorm.
 * User: martindyrby
 * Date: Feb 21, 2012
 * Time: 1:37:39 PM
 * To change this template use File | Settings | File Templates.
 */
 
class GetHighscore extends AbstractValidationCommand {

    protected $limit;

    public function required() {
        return array('limit');
    }
    
    public function optional() {
        return array();
    }
    
    public function doExecute() {
        $result = new ComandoResult();

        $scores = ORM::for_table(USER_TABLE)->order_by_desc('points')->limit($this->limit)->find_many();

        $formatted_scores = array();
        foreach($scores as $score) {
            $formatted_scores[] = $score->as_array();
        }
        $result->setData('user_scores',$formatted_scores);

        $scores = ORM::for_table(COMPANY_TABLE)->order_by_desc('points')->limit($this->limit)->find_many();

        $formatted_scores = array();
        foreach($scores as $score) {
            $formatted_scores[] = $score->as_array();
        }
        
        $result->setData('company_scores',$formatted_scores);
        return $result;
    }
}
