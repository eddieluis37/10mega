<?php

namespace App\Models;

use App\Models\Products\Meatcut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
	use HasFactory;


	protected $fillable = ['category_id', 'meatcut_id', 'categories_comerciales_id', 'subcategory_comerciales_id', 'level_product_id', 'unitofmeasure_id', 'type', 'quantity', 'name', 'code', 'barcode', 'description', 'status', 'cost', 'price_fama', 'price_insti', 'price_horeca', 'price_hogar', 'iva', 'otro_impuesto', 'stock', 'alerts', 'image'];

	protected $table = 'products';


	// Relación con categorías
	public function category()
	{
		return $this->belongsTo(Category::class, 'category_id');
	}
	
	public function inventarios()
	{
		return $this->hasMany(Inventario::class, 'product_id');
	}

	public function lotesPorVencer()
	{
		return $this->hasManyThrough(
			Lote::class,
			Inventario::class,
			'product_id',   // FK en Inventarios que referencia al Product
			'id',           // Llave primaria en Lote (Inventario.lote_id se relaciona con Lote.id)
			'id',           // Llave primaria en Product
			'lote_id'       // Campo en Inventarios que contiene el id del Lote
		)
			->whereDate('lotes.fecha_vencimiento', '>=', now())
			->orderBy('lotes.fecha_vencimiento', 'asc');
		//	->limit(1); // Solo trae el lote más próximo
	}

	/**
	 * Relación muchos a muchos con el modelo Lote.
	 */
	public function lotes()
	{
		return $this->belongsToMany(Lote::class, 'lote_products')
			->withPivot('cantidad', 'costo') // Campos adicionales en la tabla pivote
			->withTimestamps();
	}

	/**
	 * Relación uno a muchos con el modelo ProductLote.
	 */
	public function productLotes()
	{
		return $this->hasMany(ProductLote::class);
	}

	/**
	 * Relación muchos a muchos con el modelo Lote a través de la tabla `product_lote`.
	 */
	public function lotesThroughProductLote()
	{
		return $this->belongsToMany(Lote::class, 'product_lote')
			->withPivot('quantity') // Campo adicional en esta tabla pivote
			->withTimestamps();
	}	

	public function ventas()
	{
		return $this->hasMany(SaleDetail::class);
	}

	public function getImagenAttribute()
	{
		if ($this->image != null)
			return (file_exists('storage/products/' . $this->image) ? $this->image : 'noimg.jpg');
		else
			return 'noimg.jpg';
	}

	public function getPriceAttribute($value)
	{
		//comma por punto
		//return str_replace('.', ',', $value);
		// punto por coma
		return str_replace(',', '.', $value);
	}
	public function setPriceAttribute($value)
	{
		//$this->attributes['price'] = str_replace(',', '.', $value);
		$this->attributes['price_fama'] = str_replace(',', '.', preg_replace('/,/', '', $value, preg_match_all('/,/', $value) - 1));
	}


	public function centroCostos()
	{
		return $this->belongsToMany(CentroCosto::class, 'centro_costo_products', 'product_id', 'centro_costo_id')
			->withPivot('quantity');
	}

	public function centroCostoProductos()
	{
		return $this->belongsTo(Centro_costo_product::class);
	}

	public function notacredito_details()
	{
		return $this->belongsTo(NotacreditoDetail::class);
	}

	public function notadebito_details()
	{
		return $this->belongsTo(NotadebitoDetail::class);
	}

	public function compensadores_details()
	{
		return $this->hasMany(Compensadores_detail::class, 'products_id');
	}

	public function despostere()
	{
		return $this->hasOne(Despostere::class, 'products_id');
	}

	public function despostecerdo()
	{
		return $this->hasOne(Despostecerdo::class, 'products_id');
	}

	public function despostepollo()
	{
		return $this->hasOne(Despostepollo::class, 'products_id');
	}

	public function stores()
	{
		return $this->belongsToMany(Store::class, 'product_store');
	}
	// Relación: un producto pertenece a una marca
	public function brand()
	{
		return $this->belongsTo(Brand::class);
	}

	// --- COMBOS ---
	public function components(): BelongsToMany
	{
		return $this->belongsToMany(
			Product::class,
			'combo_product',
			'combo_id',
			'product_id'
		)->withPivot('quantity')
			->withTimestamps();
	}

	public function combos(): BelongsToMany
	{
		return $this->belongsToMany(
			Product::class,
			'combo_product',
			'product_id',
			'combo_id'
		)->withPivot('quantity')
			->withTimestamps();
	}

	// --- DISHES / RECETAS ---
	public function recipeItems(): BelongsToMany
	{
		return $this->belongsToMany(
			Product::class,
			'dish_product',
			'dish_id',
			'product_id'
		)->withPivot(['quantity', 'unitofmeasure_id'])
			->withTimestamps();
	}

	public function dishes(): BelongsToMany
	{
		return $this->belongsToMany(
			Product::class,
			'dish_product',
			'product_id',
			'dish_id'
		)->withPivot(['quantity', 'unitofmeasure_id'])
			->withTimestamps();
	}

/* 	public function componentes()
	{
		return $this->hasMany(Productcomposition::class, 'product_id');
	} */

	// Relación con corte de carne (si aplica)
    public function meatCut()
    {
        return $this->belongsTo(Meatcut::class, 'meatcut_id');
    }

	// Relación con unidad de medida
    public function unitofmeasure()
    {
        return $this->belongsTo(Unitofmeasure::class, 'unitofmeasure_id');
    }

	// Relación con nivel de producto
    public function levelProduct()
    {
        return $this->belongsTo(Levels_products::class, 'level_product_id');
    }

	/** Componentes asociados (para combos) **/
    public function compositions()
    {
        return $this->hasMany(Productcomposition::class, 'product_id');
    }
}
