<?php
 
class Municipio extends Eloquent {
 
    protected $table = 'municipios';
    protected $primaryKey = 'id_municipio';

    public function estado(){
    	return $this -> belongsTo('Estado');
    }
 }