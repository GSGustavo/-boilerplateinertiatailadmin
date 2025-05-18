<?php

namespace App\Http\Controllers\BackOffice;

use App\Http\Controllers\Controller;
use App\Models\Plans;
use App\Models\Subscriptions;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SubscriptionsController extends Controller
{
    public function show()
    {
        return Inertia::render("BackOffice/Admin/Subscriptions/Index");
    }

    public function save(Request $request)
    {
        // Rota para inativar, ativar, criar, editar 

        // atributos obrigatorios para o item, exceto em caso de novo item
        // id do item

        // 0 desativar
        // atributos:

        // 1 criar/editar

        $request->validate([
            'id' => 'nullable|exists:App\Models\Subscriptions,id',
            'user_id' => ['exists:App\Models\User,id'],
            'status' => ['required', 'boolean'],
        ]);

        // { severity: el.severity, summary: el.summary, detail: el.detail, life: 5000 + count }
        $data = [
            'alerts' => [],
            'hasErrors' => false,
            'errors' => [],
            'item' => null
        ];

        $model = Subscriptions::class;
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

            $dataItem = [
                'user_id' => $request->user_id,
                'status' => $request->status,
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

                $id = $request->id ? $model::find($request->id) : $saveItem->id;

                $data['item'] = Subscriptions::with(['renewals.plan'])->select(
                    'subscriptions.id as id',
                    'users.name',
                    'users.id as user_id',
                    'subscriptions.status',
                    'subscriptions.updated_at',
                    'subscriptions.created_at'

                )
                    ->where("subscriptions.id", $id)
                    ->leftJoin("users", 'users.id', 'subscriptions.user_id')
                    ->first();
            }
        }



        return response()->json($data);
    }

    public function getitems()
    {
        $items = Subscriptions::with(['renewals.plan'])->select(
            'subscriptions.id as id',
            'users.name',
            'users.id as user_id',
            'subscriptions.status',
            'subscriptions.updated_at',
            'subscriptions.created_at'

        )
            ->leftJoin("users", 'users.id', 'subscriptions.user_id')
            ->get();

        $data = [
            'items' => $items
        ];

        return response()->json($data);
    }

    public function getplansitems()
    {
        $items = Plans::select(
            'id',
            'name',
            'price',
            'days',
        )
            ->where("status", true)
            ->get();

        $data = [
            'items' => $items
        ];

        return response()->json($data);
    }
}
