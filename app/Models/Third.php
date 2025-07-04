<?php

namespace App\Models;

use App\Models\Agreement;
use App\Models\Office;
use App\Models\Type_identification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Third extends Model
{
	protected $table = 'thirds';
	
	use HasFactory;

	protected $fillable = ['name', 'type_identification_id', 'identification', 'digito_verificacion', 'office_id', 'agreement_id', 'type_regimen_iva_id', 'direccion', 'direccion1', 'direccion2', 'direccion3', 'direccion4', 'direccion5', 'direccion6', 'direccion7', 'direccion8', 'direccion9', 'direccion10', 'direccion11', 'direccion12', 'direccion13', 'direccion14', 'direccion15', 'province_id', 'celular', 'nombre_contacto', 'status', 'correo', 'porc_descuento', 'cliente', 'proveedor', 'vendedor', 'domiciliario', 'alistador', 'listaprecio_nichoid', 'listaprecio_genericid'];


	public function type_identification()
	{
		return $this->belongsTo(Type_identification::class);
	}

	public function office()
	{
		return $this->belongsTo(Office::class);
	}

	public function agreement()
	{
		return $this->belongsTo(Agreement::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}

	// Relación uno a muchos con la tabla pivote (BrandThird)
	public function brandThirds()
	{
		return $this->hasMany(BrandThird::class);
	}

	// Relación muchos a muchos con Marcas (Brand) a través de la tabla intermedia 'brand_third'
	public function brands()
	{
		return $this->belongsToMany(Brand::class, 'brand_third', 'third_id', 'brand_id')
			->withPivot('id', 'name')
			->withTimestamps();
	}
}
