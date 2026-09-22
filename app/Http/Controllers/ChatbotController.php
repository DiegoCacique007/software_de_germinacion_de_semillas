<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class ChatbotController extends Controller
{
    public function mensaje(
        Request $request,
        ChatbotService $chatbot
    ): JsonResponse{
        $user=$request->user();

        abort_unless(
            $user
            &&(
                $user->isSuperAdmin()
                ||$user->isEncargado()
            ),
            403
        );

        $validated=$request->validate([
            'mensaje'=>[
                'required',
                'string',
                'max:500'
            ]
        ]);

        try{
            $resultado=$chatbot->responder(
                $validated['mensaje'],
                $user
            );

            return response()->json([
                'ok'=>true,
                'respuesta'=>$resultado['respuesta'],
                'intencion'=>$resultado['intencion'],
                'reconocida'=>$resultado['reconocida'],
                'sugerencias'=>$resultado['sugerencias']
            ]);

        }catch(Throwable $e){
            report($e);

            return response()->json([
                'ok'=>false,
                'respuesta'=>'No pude procesar la consulta en este momento. Inténtalo nuevamente.',
                'intencion'=>'error',
                'reconocida'=>false,
                'sugerencias'=>config(
                    'microseed_chatbot.sugerencias',
                    []
                )
            ],500);
        }
    }
}
