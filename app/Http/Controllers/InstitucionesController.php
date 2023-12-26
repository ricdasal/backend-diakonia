<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Caracterizacion;
use App\Models\Clasificacion;
use App\Models\Contacto;
use App\Models\Contacto_correo;
use App\Models\Contacto_telefono;
use App\Models\Direccion;
use App\Models\Estado;
use App\Models\Institucion;
use App\Models\Red_bda;
use App\Models\Sectorizacion;
use App\Models\Tipo_poblacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Prompts\error;

class InstitucionesController extends Controller
{
    public function disableInstitucion(Request $request, $id)
    {
        $institucion = Institucion::where("id", intval($id))->first();
        $institucion->estado()->update(["nombre_estado" => "PASIVA"]);
        return response()->json(["message" => "Estado de la institucion actualizada"], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "nombre_caracterizacion" => "required",
            "nombre_actividad" => "required",
            "nombre_sectorizacion" => "required",
            "nombre_institucion" => "required",
            "representante_legal" => "required",
            "ruc" => "required",
            "numero_beneficiarios" => "required",
            "direccion_nombre" => "required",
            "url_direccion" => "required",
            "latitud" => "required",
            "longitud" => "required",
            "tipo_poblacion" => "required",
            "nombre_clasificacion" => "required",
            "nombre_estado" => "required",
            "mes_ingreso" => "required",
            "anio_ingreso" => "required",
            "nombre_contacto" => "required",
            "apellido_contacto" => "required",
            "correo_contacto" => "required",
            "telefono_contacto" => "required",
        ]);
        // $caracterizacion = Caracterizacion::updateOrCreate(['nombre_caracterizacion' => ucwords(strtolower($request->input("nombre_caracterizacion")))]);

        // $actividad = Actividad::updateOrCreate([
        //     "nombre_actividad" => ucwords(strtolower(trim($request->input("nombre_actividad")))),
        // ]);

        // $sectorizacion = Sectorizacion::updateOrCreate([
        //     "nombre_sectorizacion" => ucwords(strtolower(trim($request->input("nombre_sectorizacion")))),
        // ]);

        $institucion = Institucion::updateOrCreate(
            [
                'nombre' => trim($request->input('nombre_institucion')),
                'representante_legal' => $request->input('representante_legal'),
                'ruc' => trim($request->input('ruc')),
                'numero_beneficiarios' => intval($request->input('numero_beneficiarios'))
            ],
        );
        // $institucion->caracterizaciones()->sync($caracterizacion->id);
        // $institucion->actividades()->sync($actividad->id);
        // $institucion->sectorizaciones()->sync($sectorizacion->id);
        $institucion->caracterizaciones()->sync($request->input("nombre_caracterizacion"));
        $institucion->actividades()->sync($request->input("nombre_actividad"));
        $institucion->sectorizaciones()->sync($request->input("nombre_sectorizacion"));
        $institucion->clasificaciones()->sync($request->input("nombre_clasificacion"));

        Direccion::updateOrCreate([
            "direccion_nombre" => $request->input("direccion_nombre"),
            "url_direccion" => $request->input("url_direccion"),
            "latitud" => floatval($request->input("latitud")),
            "longitud" => floatval($request->input("longitud")),
            "institucion_id" => $institucion->id,
        ]);

        Tipo_poblacion::updateOrCreate([
            "tipo_poblacion" => trim(ucwords($request->input("tipo_poblacion"))),
            "institucion_id" => $institucion->id
        ]);

        // Clasificacion::updateOrCreate([
        //     "nombre_clasificacion" => trim(ucwords($request->input("nombre_clasificacion"))),
        //     "condicion" => trim(ucwords($request->input("condicion"))),
        //     "institucion_id" => $institucion->id
        // ]);

        Estado::updateOrCreate([
            "nombre_estado" => trim(strtoupper($request->input("nombre_estado"))),
            "institucion_id" => $institucion->id,
        ]);

        Red_bda::updateOrCreate([
            "mes_ingreso" => $request->input("mes_ingreso"),
            "anio_ingreso" => intval($request->input("anio_ingreso")),
            "institucion_id" => $institucion->id,
        ]);

        $contacto = Contacto::updateOrCreate([
            'nombre' => ucwords($request->input("nombre_contacto")),
            'apellido' => ucwords($request->input("apellido_contacto")),
            "institucion_id" => $institucion->id,
        ]);
        Contacto_correo::updateOrCreate(["correo_contacto" => $request->input("correo_contacto"), "contacto_id" => $contacto->id]);

        Contacto_telefono::updateOrCreate(["telefono_contacto" => trim($request->input("telefono_contacto")), "contacto_id" => $contacto->id]);
        return response()->json(['Institucion Registrada Sastifactoriamente' => true], 201);
    }

    public function editInstitucion(Request $request, $id)
    {
        $request->validate([
            "numero_beneficiarios" => "required",
            "nombre_contacto" => "required",
            "apellido_contacto" => "required",
            "correo_contacto" => "required",
            "telefono_contacto" => "required",
            "nombre" => "required",
            "representante_legal" => "required",
            "ruc" => "required",
            "caracterizaciones" => "required",
            "actividades" => "required",
            "sectorizaciones" => "required",
            "tipo_poblacion" => "required",
            "nombre_estado" => "required",
            "mes_ingreso" => "required",
            "anio_ingreso" => "required",
            "direccion_nombre" => "required",
            "url_direccion" => "required",
            "latitud" => "required",
            "longitud" => "required",
            "nombre_clasificacion" => "required",
        ]);
        $institucion = Institucion::find($id);
        if (!$institucion) {
            return response()->json(["message" => "No existe la institucion"], 404);
        }
        // Cualquier Role
        if (strcmp(auth()->user()->cargo_institucional, "Administrador")) {
            $institucion->update(["numero_beneficiarios" => intval($request->input('numero_beneficiarios'))]);
            $institucion->contacto()->update(["nombre" => $request->input("nombre_contacto"), "apellido" => $request->input("apellido_contacto")]);
            $contacto = Contacto::find($institucion->id);
            $contacto->contacto_correo()->update(["correo_contacto" => $request->input("correo_contacto")]);
            $contacto->contacto_telefono()->update(["telefono_contacto" => $request->input("telefono_contacto")]);
            return response()->json(["message" => "Informacion Institucion Actualizada"], 200);
        }
        // Administrador Role
        $institucion->update([
            'nombre' => trim($request->input('nombre')),
            'representante_legal' => $request->input('representante_legal'),
            'ruc' => trim($request->input('ruc')),
            'numero_beneficiarios' => intval($request->input('numero_beneficiarios'))
        ]);
        $institucion->caracterizaciones()->sync($request->input("caracterizaciones"));
        $institucion->actividades()->sync($request->input("actividades"));
        $institucion->sectorizaciones()->sync($request->input("sectorizaciones"));
        $institucion->clasificaciones()->sync($request->input("nombre_clasificacion"));

        $institucion->tipo_poblacion()->update([
            "tipo_poblacion" => trim(ucwords($request->input("tipo_poblacion"))),
        ]);
        $institucion->estado()->update([
            "nombre_estado" => trim(strtoupper($request->input("nombre_estado"))),
        ]);
        $institucion->contacto()->update([
            'nombre' => ucwords($request->input("nombre_contacto")),
            'apellido' => ucwords($request->input("apellido_contacto")),
        ]);
        $institucion->red_bda()->update([
            "mes_ingreso" => $request->input("mes_ingreso"),
            "anio_ingreso" => intval($request->input("anio_ingreso")),
        ]);
        $institucion->direccion()->update([
            "direccion_nombre" => $request->input("direccion_nombre"),
            "url_direccion" => $request->input("url_direccion"),
            "latitud" => floatval($request->input("latitud")),
            "longitud" => floatval($request->input("longitud")),
        ]);
        // $institucion->clasificacion()->update(["nombre_clasificacion" => ucwords($request->input("nombre_clasificacion"))]);
        $contacto = Contacto::where("institucion_id", "=", $institucion->id)->first();
        $contacto->contacto_correo()->update(["correo_contacto" => $request->input("correo_contacto")]);
        $contacto->contacto_telefono()->update(["telefono_contacto" => $request->input("telefono_contacto")]);
        return response()->json(["message" => "Informacion Actualizada"], 200);
    }

    public function filterInstitucion(Request $request)
    {
        $tipoPoblacion = $request->query('tipo_poblacion');
        $actividad = $request->query('nombre_actividad');
        if (is_null($tipoPoblacion) && is_null($actividad)) {
            return response('Bad Request', 400);
        }

        if (is_null($tipoPoblacion) && $actividad) {
            $instituciones = Institucion::with(['actividades', 'tipo_poblacion', 'red_bda', "clasificaciones", "sectorizaciones", "contacto", "direccion", "estado", "caracterizaciones", "contacto.contacto_correo", "contacto.contacto_telefono"])->whereHas('actividades', function ($q) use ($actividad) {
                $q->where("actividad.nombre_actividad", "=", $actividad);
            })->get();
            return response()->json(["instituciones" => $instituciones, "total" => count($instituciones)], 200);
        }

        if (is_null($actividad) && $tipoPoblacion) {
            $instituciones = Institucion::with(['actividades', 'tipo_poblacion', 'red_bda', "clasificaciones", "sectorizaciones", "contacto", "direccion", "estado", "caracterizaciones", "contacto.contacto_correo", "contacto.contacto_telefono"])->whereHas('tipo_poblacion', function ($q) use ($tipoPoblacion) {
                $q->where("tipo_poblacion.tipo_poblacion", "=", $tipoPoblacion);
            })->get();
            return response()->json(["instituciones" => $instituciones, "total" => count($instituciones)], 200);
        }

        $instituciones = Institucion::with(['actividades', 'tipo_poblacion', 'red_bda', "clasificaciones", "sectorizaciones", "contacto", "direccion", "estado", "caracterizaciones", "contacto.contacto_correo", "contacto.contacto_telefono"])
            ->whereHas('actividades', function ($q) use ($actividad) {
                $q->where("actividad.nombre_actividad", "=", $actividad);
            })->whereHas('tipo_poblacion', function ($q) use ($tipoPoblacion) {
                $q->where("tipo_poblacion.tipo_poblacion", "=", $tipoPoblacion);
            })->get();

        return response()->json(["instituciones" => $instituciones, "total" => count($instituciones)]);
    }
}
