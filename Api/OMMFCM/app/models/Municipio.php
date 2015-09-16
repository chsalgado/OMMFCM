<? php
 
class Municipio extends Eloquent {
 
    protected $table = 'municipios';

    public function estado(){
    	return $this -> belongsTo('Estado');
    }
 }