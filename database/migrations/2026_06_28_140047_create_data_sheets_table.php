<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * NOTE: Column names yahan EXACTLY same hain jo script.js ke FIELD_IDS
     * array mein hain (camelCase). Isse JS aur MySQL ke darmiyan koi
     * naam-tabdeeli (mapping) ki zaroorat nahi parti — jo data form se
     * collect hota hai wahi seedha DB column mein chala jata hai.
     */
    public function up(): void
    {
        Schema::create('data_sheets', function (Blueprint $table) {
            $table->id();

            // true  => yeh row "current draft" hai (purane IE_MW_Draft ki jagah)
            //          is naam ki sirf EK row hamesha exist karti hai.
            // false => yeh ek permanent "Saved Record" hai
            //          (purane IE_DataSheet_Backups list ki jagah)
            $table->boolean('isDraft')->default(false)->index();

            // ----- Short text fields (script.js FIELD_IDS se) -----
            // NOTE: Length 150 di hai (default 255 nahi) — wajah: utf8mb4
            // mein har character 4 bytes leta hai. 75 columns x 255 x 4 bytes
            // InnoDB ki 65,535-byte row-size limit se zyada ban jata tha
            // (error 1118). 150 chars kisi bhi field (client name, model,
            // gauge waghera) ke liye kaafi zyada hai, aur total row size ab
            // limit se kaafi neeche rehta hai.
            $shortTextFields = [
                'docDate', 'refNo', 'jobNo',
                'clientName', 'custContact', 'custPhone', 'custCity',
                'receivingDate', 'deliveryDate', 'custStatus', 'plant',
                'tagNo', 'recordId', 'serialNo', 'oem', 'model', 'eqType',
                'windingType', 'condition',
                'powerKW', 'powerHP', 'volts', 'ampere', 'rpm', 'phaseHz',
                'insClass', 'ipDuty',
                'slots', 'poles', 'gauge', 'gaugeSWG', 'typeWinding', 'connection',
                'statorLength', 'statorDia', 'coreLength', 'overhang',
                'prevWinding', 'pitchMain',
                'coilsA', 'turnsA', 'pitchA', 'setsA', 'weightPerSetA', 'wireTotalA',
                'coilsB', 'turnsB', 'pitchB', 'setsB', 'weightPerSetB', 'wireTotalB',
                'deHousing', 'ndeHousing', 'deShaft', 'ndeShaft', 'deCover',
                'ndeCover', 'keyway', 'fan', 'rotor', 'frame', 'terminalBox', 'plate',
                'bearDE', 'bearNDE',
                'ptcRequired', 'ptcType',
                'irValue', 'testVolt', 'windRes', 'noLoadI', 'vibration', 'testResult',
                'preparedBy', 'checkedBy',
            ];
            foreach ($shortTextFields as $col) {
                $table->string($col, 150)->nullable();
            }

            // ----- Long text fields (lambay notes / sentences) -----
            $longTextFields = ['gaugeDetail', 'applyDetail', 'ptcRemarks', 'failureCause', 'remarks'];
            foreach ($longTextFields as $col) {
                $table->text($col)->nullable();
            }

            // ----- Photos (base64 images + caption), JSON array -----
            $table->json('photos')->nullable();

            $table->timestamps();

            // Search/filter ke liye useful indexes (backup panel search box)
            $table->index(['clientName', 'tagNo', 'jobNo', 'refNo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_sheets');
    }
};