<?php
 
class EstadoEspecie extends Eloquent {
 
    protected $table = 'estadosEspecies';
    protected $primaryKey = 'idEstadoEspecie';
	protected $fillable = array('estado');
	
	public function especies()
	{
		return $this -> hasMany('Especie');
	}
}