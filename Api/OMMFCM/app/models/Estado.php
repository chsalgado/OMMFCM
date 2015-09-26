<?php
 
class Estado extends Eloquent {
 
    protected $table = 'estados';
    protected $primaryKey = 'id_estado';

    public function municipios(){
    	return $this -> hasMany('Municipio', 'estado');
    }
 }