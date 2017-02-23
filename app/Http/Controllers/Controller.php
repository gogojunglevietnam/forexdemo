<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $model_instance;

    public function __construct()
    {
        $class = $this->getModelName();
        $this->model_instance = new $class();
    }

    /**
     * Return class name of Model for the Controller
     *
     * @return string
     */
    private function getModelName()
    {
        // Notice: We want to get the Model name of Controller
        // We must set name of Controller and Model as
        // ABCController and ABC
        // The namespace mustbe App\Http\Controllers and App\Models

        //TODO: review this, should move hardcode to constant file
        $controller_str = "App\Http\Controllers";
        $model_str = "App\Models";
        $class = str_replace($controller_str,$model_str,get_class($this));
        $post_controller_str = "Controller";
        $class = str_replace($post_controller_str,"",$class);
        return $class;
    }

    /**
     * Get request information from request body (json)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    private function getBody(Request $request)
    {
        $items = $request->json()->all();
        // if input json is {"name":"a","val":1}
        // the return would be [["name"]=>"a",["val"]=1]
        // but we expected [[0]=> [["name"]=>"a",["val"]=1]]
        // The below code handle this case
        $keys = array_keys($items);
        if(is_string(reset($keys))) {
            //NOTICE: The condition is_string(reset($keys))
            // base on the assumption that all keys in input json are string
            return array($items);
        }
        else {
            return $items;
        }
    }

    /**
     * Get filtering condition from request url and store to
     * $filter_columns, $operators and $values
     *
     * @param  array  $inputs
     * @param  array  $columns
     * @return array
     */
    private function filtering($inputs, array $columns = array('*'))
    {
        function getOperator($flag)
        {
            switch ($flag) {
                case 1:
                    return '=';
                    break;
                case 10:
                    return '<';
                    break;
                case 11:
                    return '<=';
                    break;
                case 100:
                    return '>';
                    break;
                case 101:
                    return '>=';
                    break;
                case 110:
                    return '<>';
                    break;
                default:
                    return '=';
                    break;
            }
        }

        //TODO: review this
        $filter_columns = array();
        $values = array();
        $operators = array();
        $index = 0;
        // Notice: This code block is handle for below 3 cases:
        // .../cars?color=red
        // .../cars?seats>1&seats<=10&seats<>5
        // .../cars?id=1,2,3
        foreach($inputs as $input_key => $input_value) {
            //Check existence of >,=,< operatorss
            $larger_flg = 100*preg_match('/>/',$input_key);
            $smaller_flg = 10*preg_match('/</',$input_key);
            $equal_flag = 1-((is_string($input_value) && !strlen($input_value)));
            $flag = $larger_flg + $smaller_flg + $equal_flag;

            //set operator
            $operator = getOperator($flag);

            //set column & value
            if($flag==1) {
                // .../cars?color=red
                $column = $input_key;
                $value = $input_value;
            }
            else {
                // .../cars?seats>1&seats<=10&seats<>5
                $items = preg_split("/[<>]+/",$input_key,-1,PREG_SPLIT_NO_EMPTY);
                $column = $items[0];
                if(isset($items[1])) {
                    // .../cars?seats>1
                    $value = $items[1];
                }
                else {
                    // .../cars?seats<=10
                    $value = $input_value;
                }
            }

            if(in_array($column,$columns)) {
                //There is cases that filter condition is not valid
                // below code handle the case that $column not exist in table
                if(preg_match('/,+/',$value)) {
                    // .../cars?id=1,2,3
                    $value = explode(",",$value);
                }
                $filter_columns[$index] = $column;
                $operators[$index] = $operator;
                $values[$index] = $value;
                $index = $index + 1;
            }
        }
        $res['filter_columns'] = $filter_columns;
        $res['operators'] = $operators;
        $res['values'] = $values;
        return $res;
    }

    /**
     * Get sorting information from request url
     *
     * @param  string  $inputs
     * @return array|null
     */
    private function sorting($inputs = null)
    {
        //TODO: review this
        $results = array();
        //sort=+product_id,-id => $input = +product_id,-id
        //so we have to separate the string by ,
        if(isset($inputs)) {
            $sortConditions = explode(",",$inputs);
            foreach ($sortConditions as $sortCondition) {
                if (strchr($sortCondition,"-")) {
                    $direction = 'desc';
                }
                else {
                    $direction = 'asc';
                }
                $sortCondition = str_replace(' ','',$sortCondition);
                $sortCondition = str_replace('-','',$sortCondition);
                $results[$sortCondition] = $direction;
            }
        }
        return $results;
    }

    /**
     * Get fields information from request url
     *
     * @param  string  $inputs
     * @return array
     */
    private function fieldsSelection($inputs = null)
    {
        if(isset($inputs)) {
            return explode(",",$inputs);
        }
        else {
            return array('*');
        }
    }

    /**
     * Response error when there's no object with $id
     *
     * @param  int  $id
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    private function idInvalidJson($id, $code = 404)
    {
        $msg = 'Does not exists a object with id = '.$id;
        return response()->json(['message' => $msg], $code);
    }

    /**
     * Return url of a specific page
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    private function addPage(Request $request, $page, $last_page)
    {
        if($page > $last_page) {
            return null;
        }
        if($page < 1) {
            return null;
        }
        return $request->fullUrlWithQuery(['page' => $page]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $objects = $this->model_instance;

        //Sorting
        $sortConditions = $this->sorting($request->input('sort', null));
        foreach ($sortConditions as $sortCondition => $direction) {
            $objects = $objects->orderBy($sortCondition,$direction);
        }
        //Field selection
        $objects = $objects->select(
            $this->fieldsSelection($request->input('fields', null)));

        //Get filtering conditions
        $inputs = $request->except(['sort','fields','page','limit','offset']);
        $res = $this->filtering($inputs,$this->model_instance->getColumns());
        $filter_columns = $res['filter_columns'];
        $operators = $res['operators'];
        $values = $res['values'];
        $filter_columns_count = count($filter_columns);

        // Filtering
        for ($i = 0; $i < $filter_columns_count; $i++) {
            if(is_array($values[$i])) {
                $objects = $objects
                    ->whereIn($filter_columns[$i],$value);
            }
            else {
                $objects = $objects->where(
                    $filter_columns[$i],
                    $operators[$i],
                    $values[$i]
                );
            }
        }
        // Paging
        $offset = intval($request->input('offset',0));
        $limit = intval($request->input('limit',20));
        $page = intval($request->input('page',1));
        // TODO:Check logic here minus $offset or not
        // $total = $objects->count() - $offset;
        $total = $objects->count();
        $skip = $offset + ($page - 1) * $limit;
        if($limit == 0) {
            //When $limit = 0, return all elements
            $limit = $total;
        }
        $last_page = ceil($total/$limit);

        $response['total'] = $total;
        $response['per_page'] = $limit;
        $response['current_page'] = $page;
        $response['last_page'] = $last_page;
        $response['next_page_url'] = $this->addPage($request,$page+1,$last_page);
        $response['prev_page_url'] = $this->addPage($request,$page-1,$last_page);;
        $response['from'] = $skip + 1;
        $response['to'] = min($skip + $limit, $total);
        $response['data'] = $objects->skip($skip)->take($limit)->get()->toArray();
        return response()->json($response,201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $this->getBody($request);
        $outputs = array();
        foreach ($inputs as $input) {
            if(isset($input['id'])) {
                // Consider: should we allow user
                // to edit object $input['id'] using POST ???
                $id = $input['id'];
                $output = $this->model_instance->updateDB($id, $input);
            }
            else {
                $output = $this->model_instance->create($input)->fresh();
            }
            if($output) {
                array_push($outputs,$output);
            }
        }
        if(sizeof($outputs) == 1) {
            $outputs = reset($outputs);
        }
        return response()->json($outputs,201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $this->getBody($request)[0];
        // Consider: what happen if $input['id'] != $id ???
        if($this->model_instance->updateDB($id, $input)) {
            return response()->json([],204);
        }
        else {
            return $this->idInvalidJson($id);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->model_instance->destroy($id)) {
            // Consider: how about have object with $id but cannot delete ???
            return response()->json([],204);
        }
        else {
            return $this->idInvalidJson($id);
        }
    }

    /**
     * Display the specified resource with filtering conditions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if ($this->model_instance->find($id)) {
            //Get filtering conditions
            $inputs = $request->except(['sort','fields','page','limit','offset']);
            $res = $this->filtering($inputs,$this->model_instance->getColumns());
            $filter_columns = $res['filter_columns'];
            $operators = $res['operators'];
            $values = $res['values'];
            $filter_columns_count = count($filter_columns);
            $object = $this->model_instance->where('id',$id);

            // Filtering
            for ($i = 0; $i < $filter_columns_count; $i++) {
                $value = $values[$i];
                if(is_array($value)) {
                    $object = $object
                        ->whereIn($filter_columns[$i],$value);
                }
                else {
                    $object = $object->where(
                        $filter_columns[$i],
                        $operators[$i],
                        $values[$i]
                    );
                }
            }

            //Field selection
            $object = $object->select(
                $fields=$this->fieldsSelection($request->input('fields', null)));

            $object = $object->get()->first();
            if($object) {
                return response()->json($object,200);
            }
            else {
                $msg = 'Does not exists a object match the conditions';
                return response()->json(['message' => $msg], 404);
            }
        }
        else {
            return $this->idInvalidJson($id);
        }
    }
}
