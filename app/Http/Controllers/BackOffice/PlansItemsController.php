<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\PlansItems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlansItemsController extends Controller
{
    public function delete(Request $request)
    {

        $data = [
            'alerts' => [],
            'hasErrors' => false,
            'errors' => [],
            'item' => null
        ];

        $data['hasErrors'] = true;

        $data['errors'][] = [
            "severity" => 'error',
            "summary" => "Erro",
            'detail' => 'Houve um erro ao salvar o item, tente novamente mais tarde!'
        ];


        $request->validate([
            'id' => ['required', 'exists:App\Models\PlansItems,id'],
        ]);


        $item = PlansItems::find($request->id);

        $plan = Plans::find($item->plan_id);


        if ($plan) {


            if ($item->delete()) {

                $data['hasErrors'] = false;

                $data['errors'] = [];

                $data['alerts'][] = [
                    "severity" => 'success',
                    "summary" => "Sucesso!",
                    'detail' => 'Operação feita com sucesso!'
                ];
            }
        }

        return response()->json($data);
    }
}
