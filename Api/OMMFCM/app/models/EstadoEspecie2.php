<?php
 
class EstadoEspecie2 extends Eloquent {
 
    protected $table = 'estadosEspecies2';
    protected $primaryKey = 'idEstadoEspecie2';
	protected $fillable = array('estado');
	
	public function especies()
	{
		return $this -> hasMany('Especie');
	}
}