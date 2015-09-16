<? php
 
class Estado extends Eloquent {
 
    protected $table = 'estados';

    public function municipios(){
    	return $this -> hasMany('Municipio');
    }
 }