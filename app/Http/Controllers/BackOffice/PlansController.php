<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\PlansItems;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PlansController extends Controller
{
    public function show()
    {
        return Inertia::render("BackOffice/Admin/Plans/Index");
    }

    public function save(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:App\Models\Plans,id',
            'name' => ['required', 'max:255'],
            'status' => 'boolean',
            'internal' => 'boolean',
            'price' => 'nullable|regex:/^\d{1,3}(\.\d{3})*(,\d{2})?$/',
            'days' => 'required|integer',
            'planitems' => 'nullable|json',
        ]);


        // { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }
        $data = [
            'alerts' => [],
            'hasErrors' => false,
            'errors' => [],
            'item' => null
        ];

        $model = Plans::class;
        $item = new $model;

        $saveItem = false;
        $savePlanItem = false;

        if ($request->operation == 0) {
            $item = $model::find($request->id);

            // $menu = Menu::select()->where('icon_id', $request->id)->where("status", "1")->exists();
            // $services = Services::select()->where('icon_id', $request->id)->where("status", "1")->exists();

            // if (!$menu and !$services) {
            //     $saveItem = $model->delete();

            //     if ($saveItem) {
            //         $data['status'] = true;
            //     }
            // } else {
            //     $data['msg'] = 'O ícone tem vínculos, não é possível apaga-lo!';
            // }

        } else {
            $saveItem = false;
            $data['new'] = false;

            $price = $request->price;
            if ($request->price) {
                $price = str_replace('.', '', $price);
                $price = str_replace(',', '.', $price);

                $price = floatval(number_format($price, 2));
            }

            $dataItem = [
                'name' => $request->name,
                'status' => $request->status,
                'internal' => $request->internal,
                'price' => $price,
                'days' => $request->days,
            ];

            if ($request->id) {

                $item = $model::find($request->id);

                if (!$item) {
                    $data['hasErrors'] = true;
                    // { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }

                    $data['errors'][] = [
                        "severity" => 'error',
                        "summary" => "Erro",
                        'detail' => 'Houve um erro ao salvar o item, tente novamente mais tarde 1!'
                    ];
                } else {
                    $dataItem['id'] = $item->id;

                    $saveItem = $item->update($dataItem);
                }
            } else {
                // Aqui o item ja é criado ativo
                $data['new'] = true;

                $saveItem = $item::create($dataItem);
            }

            $planitems = json_decode($request->planitems);

            if ($saveItem and $request->planitems) {
                $planitemsmodel = new PlansItems;

                foreach ($planitems as $key => $planitem) {
                    $dataPlanItem = [
                        'name' => $planitem->name,
                        'plan_id' => $request->id ?? $saveItem->id,
                    ];

                    if ($planitem->id) {
                        $dataPlanItem['id'] = $planitem->id;

                        $planitemItem = $planitemsmodel->find($planitem->id);

                        if ($planitemItem) {
                            $savePlanItemLocal = $planitemItem->update($dataPlanItem);
                        }
                    } else {
                        $savePlanItemLocal = $planitemsmodel::create($dataPlanItem);
                    }

                    if ($savePlanItem and !$savePlanItemLocal) {
                        $savePlanItem = false;
                    }
                }
            }
        }

        if (!$saveItem and !$savePlanItem) {
            $data['hasErrors'] = true;

            $data['errors'][] = [
                "severity" => 'error',
                "summary" => "Erro",
                'detail' => 'Houve um erro ao salvar o item, tente novamente mais tarde 2!'
            ];
        } else {

            $data['alerts'][] = [
                "severity" => 'success',
                "summary" => "Sucesso!",
                'detail' => 'Operação feita com sucesso!'
            ];

            $data['item'] = $request->id ? $model::find($request->id) : $saveItem;

            $data['planitems'] = PlansItems::select('id', 'name')
                ->where("plan_id", $request->id ?? $saveItem->id)
                ->get();
        }

        return response()->json($data);
    }

    public function getitems()
    {
        $items = Plans::with(['plan_items'])
            ->select('id', 'name', 'days', 'price', 'internal', 'status', 'created_at', 'updated_at')
            ->get();

        $data = [
            'items' => $items
        ];

        return response()->json($data);
    }
}
