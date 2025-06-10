<?php

/**
 * Created by Reliese Model.
 */

namespace App\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Operation
 * 
 * @property int $id
 * @property float|null $montant
 * @property string|null $libelle
 * @property bool|null $typeOperation
 * @property int $idFamille
 * @property int $idCompte
 * 
 * @property Famille $famille
 * @property Compte $compte
 *
 * @package App\models
 */
class Operation extends Model
{
	protected $table = 'operation';
	public $timestamps = false;

	protected $casts = [
		'montant' => 'float',
		'typeOperation' => 'bool',
		'idFamille' => 'int',
		'idCompte' => 'int'
	];

	protected $fillable = [
		'montant',
		'libelle',
		'typeOperation',
		'idFamille',
		'idCompte'
	];

	public function famille()
	{
		return $this->belongsTo(Famille::class, 'idFamille');
	}

	public function compte()
	{
		return $this->belongsTo(Compte::class, 'idCompte');
	}
}
