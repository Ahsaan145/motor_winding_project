<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSheet extends Model
{
    use HasFactory;

    protected $table = 'data_sheets';

    protected $fillable = [
        'isDraft',
        'docDate', 'refNo', 'jobNo', 'clientName', 'custContact', 'custPhone', 
        'custCity', 'receivingDate', 'deliveryDate', 'custStatus', 'plant',
        'tagNo', 'recordId', 'serialNo', 'oem', 'model', 'eqType', 'windingType', 
        'condition', 'powerKW', 'powerHP', 'volts', 'ampere', 'rpm', 'phaseHz',
        'insClass', 'ipDuty', 'slots', 'poles', 'gauge', 'gaugeSWG', 'typeWinding', 
        'connection', 'statorLength', 'statorDia', 'coreLength', 'overhang',
        'prevWinding', 'pitchMain', 'coilsA', 'turnsA', 'pitchA', 'setsA', 
        'weightPerSetA', 'wireTotalA', 'coilsB', 'turnsB', 'pitchB', 'setsB', 
        'weightPerSetB', 'wireTotalB', 'deHousing', 'ndeHousing', 'deShaft', 
        'ndeShaft', 'deCover', 'ndeCover', 'keyway', 'fan', 'rotor', 'frame', 
        'terminalBox', 'plate', 'bearDE', 'bearNDE', 'ptcRequired', 'ptcType',
        'irValue', 'testVolt', 'windRes', 'noLoadI', 'vibration', 'testResult',
        'preparedBy', 'checkedBy', 'gaugeDetail', 'applyDetail', 'ptcRemarks', 
        'failureCause', 'remarks', 'photos'
    ];

    protected $casts = [
        'isDraft' => 'boolean',
        'photos' => 'array',        // JSON field ko array mein convert karega
    ];
}