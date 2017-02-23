<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class CommonModel extends Model
{
    protected static $unguarded = true;
    /**
     * Get columns information of table
     *
     * @return array
     */
    public function getColumns()
    {
        // Consider: Can we have better solution ???
        //  don't use SchemaBuilder but Eloquent
        return DB::connection($this->connection)->getSchemaBuilder()
          ->getColumnListing($this->getTable());
        // In addition, this only get the column name!
        // We can get column type using below code
        // $columns = DB::select('show columns from ' . $table_name);
        // foreach ($columns as $value) {
        //     $name = $value->Field;
        //     $type = $value->Type;
        // }
    }

    /**
     * Update database record
     *
     * @param  int  $id
     * @param  array  $inputs
     * @param  boolean  $timestamps
     * @return App\Models\CommonModel|null
     */
    public function updateDB($id, $input)
    {
        $res = $this->find($id);
        if($res) {
            unset($input['id']);
            $res->update($input);
            return $res->fresh();
        }
        return null;
    }
}
