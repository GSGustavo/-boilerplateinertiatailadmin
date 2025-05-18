<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionRenewals;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionRenewalsController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'plan_id' => ['required', 'exists:App\Models\Plans,id'],
            'start_on' => ['required', Rule::date()->format('Y-m-d H:i:s')],
            'paid_at' => ['nullable', Rule::date()->format('Y-m-d H:i:s')],
            'end_on' => ['nullable', Rule::date()->format('Y-m-d H:i:s')],
            'subscription_id' => ['nullable', 'exists:App\Models\Subscriptions,id'],
            'id' => 'nullable|exists:App\Models\SubscriptionRenewals,id',
            'status' => 'boolean',
            'price' => 'nullable|regex:/^\d{1,3}(\.\d{3})*(,\d{2})?$/',
        ]);

          // { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }
          $data = [
            'alerts' => [],
            'hasErrors' => false,
            'errors' => [],
            'item' => null
        ];

        $model = SubscriptionRenewals::class;
        $item = new $model;


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
                'plan_id' => $request->plan_id,
                'start_on' => $request->start_on,
                'end_on' => $request->end_on,
                'subscription_id' => $request->subscription_id,
                'status' => $request->status,
                'price' => $price,
                'paid_at' => $request->paid_at
            ];

            if ($request->id) {

                $item = $model::find($request->id);

                if (!$item) {
                    $data['hasErrors'] = true;
                    // { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }

                    $data['errors'][] = [
                        "severity" => 'error',
                        "summary" => "Erro",
                        'detail' => 'Houve um erro ao salvar o item, tente novamente mais tarde!'
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

            if (!$saveItem) {
                $data['hasErrors'] = true;

                // { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }
                $data['errors'][] = [
                    "severity" => 'error',
                    "summary" => "Erro",
                    'detail' => 'Houve um erro ao salvar o item, tente novamente mais tarde!'
                ];
            } else {

                //  { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }
                $data['alerts'][] = [
                    "severity" => 'success',
                    "summary" => "Sucesso!",
                    'detail' => 'Item salvo com sucesso!'
                ];

                $data['item'] = $request->id ? $model::find($request->id) : $saveItem;

                // $id = $id = $request->id ? $model::find($request->id) : $saveItem->id;




            }
        }



        return response()->json($data);

    }

    
}
