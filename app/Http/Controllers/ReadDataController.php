<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Actividad_institucion;
use App\Models\Caracterizacion_institucion;
use App\Models\Clasificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Agrega esta línea para importar la clase Auth
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response; // Agrega esta línea para importar la clase Response
use App\Models\Institucion;
use App\Models\Contacto;
use App\Models\Contacto_correo;
use App\Models\Contacto_telefono;
use App\Models\Direccion;
use App\Models\Estado;
use App\Models\Red_bda;
use App\Models\Sectorizacion_institucion;
use App\Models\Tipo_poblacion;
use App\Models\Caracterizacion;
use App\Models\Sectorizacion;
use PhpParser\Node\Stmt\TryCatch;

use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class ReadDataController extends Controller
{
    public function readData(Request $request)
    {
        // try {
        $request->validate([
            "data" => "required"
        ]);
        $data = $request->input('data');
        foreach ($data as $row) {

            $caracterizacion = Caracterizacion::updateOrCreate(['nombre_caracterizacion' => ucwords(strtolower($row["caracterización"]))]);


            $actividad = Actividad::updateOrCreate([
                "nombre_actividad" => ucwords(strtolower(trim($row["actividad"]))),
            ]);

            $sectorizacion = Sectorizacion::updateOrCreate([
                "nombre_sectorizacion" => ucwords(strtolower(trim($row["sectorización"]))),
            ]);

            $institucion = Institucion::updateOrCreate(
                [
                    'nombre' => trim($row['nombre_de_las_instituciones']),
                    'representante_legal' => $row['representante_legal'],
                    'ruc' => trim($row['ruc']),
                    'numero_beneficiarios' => intval($row['número_de_beneficiarios'])
                ],
            );

            // if (isset($row['clasificación'])) {
            //     Clasificacion::updateOrCreate([
            //         "nombre_clasificacion" => trim(ucwords($row["clasificación"])),
            //         "condicion" => false,
            //         "institucion_id" => $institucion->id
            //     ]);
            // }
            $clasificacionesIds = [];
            if (isset($row["salud"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Salud")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            if (isset($row["rehabilitacion_social"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Rehabilitacion Social")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            if (isset($row["exclusión_social"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Exclusión Social")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            if (isset($row["inseguridad_alimentaria"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Inseguridad Alimentaria")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            if (isset($row["situación_de_calle"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Situación De Calle")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            if (isset($row["albergues"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Albergues")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            if (isset($row["discapacidad"])) {
                $clasificacion = Clasificacion::where("nombre_clasificacion", "=", "Discapacidad")->first();
                array_push($clasificacionesIds, $clasificacion->id);
            }

            $institucion->caracterizaciones()->sync($caracterizacion->id);
            $institucion->actividades()->sync($actividad->id);
            $institucion->sectorizaciones()->sync($sectorizacion->id);
            if (!is_null($clasificacionesIds)) {
                $institucion->clasificaciones()->sync($clasificacionesIds);
            }
            if (isset($row['dirección'])) {
                $coords = explode(",", $row["latitud_y_longitud"]);
                Direccion::updateOrCreate([
                    "direccion_nombre" => $row["dirección"],
                    "url_direccion" => $row["direccion_(google_maps)"],
                    "latitud" => floatval($coords[0]),
                    "longitud" => floatval($coords[1]),
                    "institucion_id" => $institucion->id,
                ]);
            }

            if (isset($row['tipo_de_población'])) {
                Tipo_poblacion::updateOrCreate([
                    "tipo_poblacion" => trim(ucwords($row["tipo_de_población"])),
                    "institucion_id" => $institucion->id
                ]);
            }

            if (isset($row['estatus'])) {
                Estado::updateOrCreate([
                    "nombre_estado" => trim(strtoupper($row["estatus"])),
                    "institucion_id" => $institucion->id,
                ]);
            }

            if (isset($row['mes_de_ingreso_red_bda'])) {
                Red_bda::updateOrCreate([
                    "mes_ingreso" => $row["mes_de_ingreso_red_bda"],
                    "anio_ingreso" => intval($row["año_de_ingreso_red_bda"]),
                    "institucion_id" => $institucion->id,
                ]);
            }

            if (isset($row['contacto'])) {
                $contacto_data = explode(" ", $row["contacto"], 2);
                $contacto = Contacto::updateOrCreate([
                    'nombre' => ucwords($contacto_data[0]),
                    'apellido' => ucwords($contacto_data[1]),
                    "institucion_id" => $institucion->id,
                ]);
                Contacto_correo::updateOrCreate(["correo_contacto" => trim($row["correos"] ?? "sin especificar"), "contacto_id" => $contacto->id]);

                Contacto_telefono::updateOrCreate(["telefono_contacto" => trim($row["teléfono"] ?? "sin especificar"), "contacto_id" => $contacto->id]);
            }
        }
        return response()->json(['success' => true]);
        // } catch (\Exception $e) {
        //     error_log($e->getMessage());
        //     return response()->json(["message" => "Estamos fuera de servicio, intenta mas tarde."], 500);
        // }
    }

    public function AllData()
    {
        return Institucion::all();
    }

    public function AllInstituciones(Request $request)
    {
        $query = DB::table('institucion')
            ->join('estado', 'institucion.id', '=', 'estado.institucion_id')
            ->orderBy('updated_at', 'DESC')
            ->get();

        return $query->toArray();
    }

    public function DataInstituciones(Request $request)
    {
        $instituciones = DB::table('institucion')->orderBy('updated_at', 'desc')
            ->get();

        foreach ($instituciones as $institucion) {
            $actividades = DB::table('actividad')
                ->join('actividad_institucion', 'actividad.id', '=', 'actividad_institucion.actividad_id')
                ->where('actividad_institucion.institucion_id', $institucion->id)
                ->select('actividad.*')
                ->get();

            $institucion->actividades = $actividades;
        }

        foreach ($instituciones as $institucion) {
            $caracterizacion = DB::table('caracterizacion')
                ->join('caracterizacion_institucion', 'caracterizacion.id', '=', 'caracterizacion_institucion.caracterizacion_id')
                ->where('caracterizacion_institucion.institucion_id', $institucion->id)
                ->select('caracterizacion.*')
                ->get();

            $institucion->caracterizacion = $caracterizacion;
        }

        foreach ($instituciones as $institucion) {
            $sectorizacion = DB::table('sectorizacion')
                ->join('sectorizacion_institucion', 'sectorizacion.id', '=', 'sectorizacion_institucion.sector_id')
                ->where('sectorizacion_institucion.institucion_id', $institucion->id)
                ->select('sectorizacion.*')
                ->get();

            $institucion->sectorizacion = $sectorizacion;
        }

        /*
        foreach ($instituciones as $institucion) {
            $clasificacion = DB::table('clasificacion')
                ->join('clasificacion_institucion', 'clasificacion.id', '=', 'clasificacion_institucion.clasificacion_id')
                ->where('clasificacion_institucion.institucion_id', $institucion->id)
                ->select('clasificacion.*')
                ->get();

            $institucion->clasificacion = $clasificacion;
        }
        */

        foreach ($instituciones as $institucion) {
            $tipos_poblacion = DB::table('tipo_poblacion')
                ->where('tipo_poblacion.institucion_id', $institucion->id)
                ->select('tipo_poblacion.*')
                ->get();

            $institucion->tipos_poblacion = $tipos_poblacion;
        }

        foreach ($instituciones as $institucion) {
            $estado = DB::table('estado')
                ->where('estado.institucion_id', $institucion->id)
                ->select('estado.*')
                ->get();

            $institucion->estado = $estado;
        }

        foreach ($instituciones as $institucion) {
            $direccion = DB::table('direccion')
                ->where('direccion.institucion_id', $institucion->id)
                ->select('direccion.*')
                ->get();

            $institucion->direccion = $direccion;
        }

        foreach ($instituciones as $institucion) {
            $red_bda = DB::table('red_bda')
                ->where('red_bda.institucion_id', $institucion->id)
                ->select('red_bda.*')
                ->get();

            $institucion->red_bda = $red_bda;
        }

        foreach ($instituciones as $institucion) {
            $contactos = DB::table('contacto')
                ->where('contacto.institucion_id', $institucion->id)
                ->select('contacto.*')
                ->get();

            foreach ($contactos as $contacto) {
                $correos = DB::table('contacto_correo')
                    ->where('contacto_correo.contacto_id', $contacto->id)
                    ->select('contacto_correo.correo_contacto')
                    ->get();

                $telefonos = DB::table('contacto_telefono')
                    ->where('contacto_telefono.contacto_id', $contacto->id)
                    ->select('contacto_telefono.telefono_contacto')
                    ->get();

                $contacto->correos = $correos;
                $contacto->telefonos = $telefonos;
            }

            $institucion->contactos = $contactos;
        }

        return $instituciones->toArray();
    }

    public function DataInstitucionesDirecciones(Request $request)
    {
        $instituciones = DB::table('institucion')->get();

        foreach ($instituciones as $institucion) {
            $direccion = DB::table('direccion')
                ->where('direccion.institucion_id', $institucion->id)
                ->select('direccion.*')
                ->get();

            $institucion->direccion = $direccion;
        }

        return $instituciones->toArray();
    }


    public function DataInstitucionesId(Request $request, string $id)
    {
        $instituciones = DB::table('institucion')
            ->where('institucion.id', $id)
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($instituciones as $institucion) {
            $actividades = DB::table('actividad')
                ->join('actividad_institucion', 'actividad.id', '=', 'actividad_institucion.actividad_id')
                ->where('actividad_institucion.institucion_id', $institucion->id)
                ->select('actividad.*')
                ->get();

            $institucion->actividades = $actividades;
        }

        foreach ($instituciones as $institucion) {
            $caracterizacion = DB::table('caracterizacion')
                ->join('caracterizacion_institucion', 'caracterizacion.id', '=', 'caracterizacion_institucion.caracterizacion_id')
                ->where('caracterizacion_institucion.institucion_id', $institucion->id)
                ->select('caracterizacion.*')
                ->get();

            $institucion->caracterizacion = $caracterizacion;
        }

        foreach ($instituciones as $institucion) {
            $sectorizacion = DB::table('sectorizacion')
                ->join('sectorizacion_institucion', 'sectorizacion.id', '=', 'sectorizacion_institucion.sector_id')
                ->where('sectorizacion_institucion.institucion_id', $institucion->id)
                ->select('sectorizacion.*')
                ->get();

            $institucion->sectorizacion = $sectorizacion;
        }

        foreach ($instituciones as $institucion) {
            $clasificacion = DB::table('clasificacion')
                ->join('clasificacion_institucion', 'clasificacion.id', '=', 'clasificacion_institucion.clasificacion_id')
                ->where('clasificacion_institucion.institucion_id', $institucion->id)
                ->select('clasificacion.*')
                ->get();

            $institucion->clasificacion = $clasificacion;
        }

        foreach ($instituciones as $institucion) {
            $tipos_poblacion = DB::table('tipo_poblacion')
                ->where('tipo_poblacion.institucion_id', $institucion->id)
                ->select('tipo_poblacion.*')
                ->get();

            $institucion->tipos_poblacion = $tipos_poblacion;
        }

        foreach ($instituciones as $institucion) {
            $estado = DB::table('estado')
                ->where('estado.institucion_id', $institucion->id)
                ->select('estado.*')
                ->get();

            $institucion->estado = $estado;
        }

        foreach ($instituciones as $institucion) {
            $direccion = DB::table('direccion')
                ->where('direccion.institucion_id', $institucion->id)
                ->select('direccion.*')
                ->get();

            $institucion->direccion = $direccion;
        }

        foreach ($instituciones as $institucion) {
            $red_bda = DB::table('red_bda')
                ->where('red_bda.institucion_id', $institucion->id)
                ->select('red_bda.*')
                ->get();

            $institucion->red_bda = $red_bda;
        }

        foreach ($instituciones as $institucion) {
            $contactos = DB::table('contacto')
                ->where('contacto.institucion_id', $institucion->id)
                ->select('contacto.*')
                ->get();

            foreach ($contactos as $contacto) {
                $correos = DB::table('contacto_correo')
                    ->where('contacto_correo.contacto_id', $contacto->id)
                    ->select('contacto_correo.correo_contacto')
                    ->get();

                $telefonos = DB::table('contacto_telefono')
                    ->where('contacto_telefono.contacto_id', $contacto->id)
                    ->select('contacto_telefono.telefono_contacto')
                    ->get();

                $contacto->correos = $correos;
                $contacto->telefonos = $telefonos;
            }

            $institucion->contactos = $contactos;
        }

        return $instituciones->toArray();
    }

    public function obtenerCaracterizaciones()
    {
        $caracterizaciones = DB::table('caracterizacion')->get();
        return $caracterizaciones->toArray();
    }

    public function obtenerSectores()
    {
        $sectores = DB::table('sectorizacion')->get();
        return $sectores->toArray();
    }

    public function obtenerActividades()
    {
        $actividades = DB::table('actividad')->get();
        return $actividades->toArray();
    }

    public function registrarInstitucion(Request $request)
    {

        // try{



        // }catch (\Exception $e) {
        //     info($e);

        // }

        $institucion = Institucion::create([
            "nombre" => $request->input('nombre_institucion'),
            "representante_legal" => $request->input('representante_legal'),
            "ruc" => $request->input('ruc'),
            "numero_beneficiarios" => $request->input('numero_beneficiarios')
        ]);

        $id_institucion = $institucion->id;

        if (!$id_institucion) {
            throw new \Exception('Error al obtener el ID de la institución.');
        }

        $lista_actividades = $request->input('actividad_id');
        $total_lista_actividades = count($lista_actividades);
        for ($i = 0; $i <  $total_lista_actividades; $i++) {
            $actividad_institucion = Actividad_institucion::create([
                'actividad_id' =>  $lista_actividades[$i],
                'institucion_id' => $institucion->id
            ]);
        }


        // $caracterizacion_institucion = Caracterizacion_institucion::create([
        //     'caracterizacion_id' => $request->input('caracterizacion_id'),
        //     'institucion_id' => $id_institucion,
        // ]);

        // $lista_condiciones = $request->input('condicion');
        // $total_condiciones = count($lista_condiciones);
        // for($i = 0; $i < $total_condiciones; $i++){
        //     $clasificacion = Clasificacion::create([
        //     'nombre_clasificacion' => $lista_condiciones[$i],
        //     'condicion' => true,
        //     'institucion_id' => $id_institucion

        // ]);

        // }

        $clasificacion = Clasificacion::create([
            'nombre_clasificacion' => $request->input('condicion'),
            'condicion' => true,
            'institucion_id' => $id_institucion

        ]);

        $contacto = Contacto::create([
            'nombre' => $request->input('nombre_contacto'),
            'apellido' => $request->input('apellido_contacto'),
            'institucion_id' => $id_institucion,

        ]);

        $id_contacto = $contacto->id;
        if (!$id_contacto) {
            throw new \Exception('Error al obtener el ID del contacto.');
        }


        $correo_contacto = Contacto_correo::create([
            'correo_contacto' => $request->input('correo_contacto'),
            'contacto_id' => $id_contacto
        ]);

        $contacto_telefono = Contacto_telefono::create([
            'telefono_contacto' => $request->input('telefono_contacto'),
            'contacto_id' => $id_contacto
        ]);

        $direccion = Direccion::create([
            'direccion_nombre' => $request->input('direccion'),
            'url_direccion' => $request->input('url_direccion'),
            'latitud' => $request->input('latitud'),
            'longitud' => $request->input('longitud'),
            'institucion_id' => $id_institucion,
        ]);

        $estado = Estado::create([
            'nombre_estado' => $request->input('nombre_estado'),
            'institucion_id' => $id_institucion,
        ]);

        $red_bda = Red_bda::create([
            'mes_ingreso' => $request->input('mes_ingreso'),
            'anio_ingreso' => $request->input('anio_ingreso'),
            'institucion_id' => $id_institucion,

        ]);

        $sectorizacion = Sectorizacion_institucion::create([
            'sector_id' => $request->input('sector_id'),
            'institucion_id' => $id_institucion
        ]);


        $lista_tipo_poblacion = $request->input('tipo_poblacion');
        $total_tipo_poblacion = count($lista_tipo_poblacion);
        for ($i = 0; $i < $total_tipo_poblacion; $i++) {
            $tipo_poblacion = Tipo_poblacion::create([
                'tipo_poblacion' => $lista_tipo_poblacion[$i],
                'institucion_id' => $id_institucion

            ]);
        }

        // $tipo_poblacion = Tipo_poblacion::create([
        //     'tipo_poblacion' => ,
        //     'institucion_id' => $id_institucion

        // ]);

        return response([
            'message' => 'OK',
            'institucion' => $institucion,
            'caracterizacion_institucion' => $institucion

        ], Response::HTTP_ACCEPTED);
    }

    public function obtenerTiposPoblacion(Request $request)
    {
        $tiposPoblacion = DB::table('tipo_poblacion')->distinct('tipo_poblacion')->get()->toArray();
        return response()->json($tiposPoblacion, 200);
    }

    public function getAllInformation(Request $request)
    {
        $tiposPoblacion = DB::table('tipo_poblacion')->distinct('tipo_poblacion')->get()->toArray();
        $clasificaciones = DB::table('clasificacion')->distinct('nombre_clasificacion')->get()->toArray();
        $actividades = DB::table('actividad')->get()->toArray();
        $sectorizaciones = DB::table('sectorizacion')->get()->toArray();
        $estados = DB::table('estado')->distinct('nombre_estado')->get()->toArray();
        $caracterizaciones = DB::table('caracterizacion')->get()->toArray();
        return response()->json(["tipos_poblacion" => $tiposPoblacion, "caracterizaciones" => $caracterizaciones, "actividades" => $actividades, "sectorizaciones" => $sectorizaciones, "estados" => $estados, "clasificaciones" => $clasificaciones], 200);
    }
}
