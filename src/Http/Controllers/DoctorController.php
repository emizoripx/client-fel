<?php

namespace EmizorIpx\ClientFel\Http\Controllers;

use App\Http\Controllers\BaseController;
use EmizorIpx\ClientFel\Models\FelDoctor;
use EmizorIpx\ClientFel\Models\FelDoctorKardexSummary;
use EmizorIpx\ClientFel\Models\FelDoctorKardexMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DoctorController extends BaseController
{
    protected function getCompanyId()
    {
        $user = auth()->user();
        if ($user && method_exists($user, 'company') && $user->company()) {
            return $user->company()->id;
        }
        return request()->get('company_id', null);
    }

    public function index(Request $request)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return response()->json(['error' => 'Compañía no identificada'], 400);
        }

        $query = FelDoctor::byCompany($companyId);

        $search = $request->query('search', $request->query('filter', null));
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre_apellido', 'like', "%{$search}%")
                  ->orWhere('nit_documento', 'like', "%{$search}%")
                  ->orWhere('nro_matricula', 'like', "%{$search}%")
                  ->orWhere('especialidad', 'like', "%{$search}%");
            });
        }

        if ($request->has('activo')) {
            $activo = filter_var($request->query('activo'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($activo !== null) {
                $query->where('activo', $activo);
            }
        }

        $especialidad = $request->query('especialidad', null);
        if (!empty($especialidad) && $especialidad !== 'all') {
            $query->where('especialidad', $especialidad);
        }

        $query->orderBy('nombre_apellido', 'asc');

        $perPage = $request->query('per_page', null);
        if ($perPage && is_numeric($perPage)) {
            $doctors = $query->paginate((int) $perPage);
            return response()->json([
                'data' => $doctors->items(),
                'meta' => [
                    'current_page' => $doctors->currentPage(),
                    'last_page' => $doctors->lastPage(),
                    'per_page' => $doctors->perPage(),
                    'total' => $doctors->total(),
                ]
            ]);
        }

        $doctors = $query->get();
        return response()->json([
            'data' => $doctors,
            'total' => $doctors->count()
        ]);
    }

    public function show(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $doctor = FelDoctor::byCompany($companyId)->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Médico no encontrado'], 404);
        }

        return response()->json(['data' => $doctor]);
    }

    public function store(Request $request)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return response()->json(['error' => 'Compañía no identificada'], 400);
        }

        $validator = Validator::make($request->all(), [
            'nombre_apellido' => 'required|string|max:255',
            'nit_documento' => 'required|string|max:50',
            'nro_matricula' => 'nullable|string|max:50',
            'especialidad' => 'nullable|string|max:150',
            'especialidad_detalle' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nit = trim($request->input('nit_documento'));

        // Verificar si ya existe un médico activo con este NIT en la misma empresa
        $existing = FelDoctor::byCompany($companyId)
            ->where('nit_documento', $nit)
            ->first();

        if ($existing) {
            return response()->json([
                'error' => "Ya existe un médico registrado con el documento/NIT {$nit}."
            ], 422);
        }

        $doctor = FelDoctor::create([
            'company_id' => $companyId,
            'nombre_apellido' => trim($request->input('nombre_apellido')),
            'nit_documento' => $nit,
            'nro_matricula' => trim($request->input('nro_matricula', '')) ?: null,
            'especialidad' => trim($request->input('especialidad', '')) ?: 'Medicina General',
            'especialidad_detalle' => trim($request->input('especialidad_detalle', '')) ?: null,
            'telefono' => trim($request->input('telefono', '')) ?: null,
            'email' => trim($request->input('email', '')) ?: null,
            'direccion' => trim($request->input('direccion', '')) ?: null,
            'activo' => $request->has('activo') ? (bool) $request->input('activo') : true,
        ]);

        return response()->json([
            'data' => $doctor,
            'message' => 'Médico registrado con éxito'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $doctor = FelDoctor::byCompany($companyId)->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Médico no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre_apellido' => 'sometimes|required|string|max:255',
            'nit_documento' => 'sometimes|required|string|max:50',
            'nro_matricula' => 'nullable|string|max:50',
            'especialidad' => 'nullable|string|max:150',
            'especialidad_detalle' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:150',
            'direccion' => 'nullable|string|max:255',
            'activo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('nit_documento')) {
            $nit = trim($request->input('nit_documento'));
            $conflict = FelDoctor::byCompany($companyId)
                ->where('nit_documento', $nit)
                ->where('id', '!=', $id)
                ->first();

            if ($conflict) {
                return response()->json([
                    'error' => "Ya existe otro médico con el documento/NIT {$nit}."
                ], 422);
            }
            $doctor->nit_documento = $nit;
        }

        if ($request->has('nombre_apellido')) {
            $doctor->nombre_apellido = trim($request->input('nombre_apellido'));
        }
        if ($request->has('nro_matricula')) {
            $doctor->nro_matricula = trim($request->input('nro_matricula', '')) ?: null;
        }
        if ($request->has('especialidad')) {
            $doctor->especialidad = trim($request->input('especialidad', '')) ?: 'Medicina General';
        }
        if ($request->has('especialidad_detalle')) {
            $doctor->especialidad_detalle = trim($request->input('especialidad_detalle', '')) ?: null;
        }
        if ($request->has('telefono')) {
            $doctor->telefono = trim($request->input('telefono', '')) ?: null;
        }
        if ($request->has('email')) {
            $doctor->email = trim($request->input('email', '')) ?: null;
        }
        if ($request->has('direccion')) {
            $doctor->direccion = trim($request->input('direccion', '')) ?: null;
        }
        if ($request->has('activo')) {
            $doctor->activo = (bool) $request->input('activo');
        }

        $doctor->save();

        return response()->json([
            'data' => $doctor,
            'message' => 'Médico actualizado con éxito'
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $doctor = FelDoctor::byCompany($companyId)->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Médico no encontrado'], 404);
        }

        $doctor->activo = !$doctor->activo;
        $doctor->save();

        return response()->json([
            'data' => $doctor,
            'message' => $doctor->activo ? 'Médico activado' : 'Médico inactivado'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        $doctor = FelDoctor::byCompany($companyId)->where('id', $id)->first();

        if (!$doctor) {
            return response()->json(['error' => 'Médico no encontrado'], 404);
        }

        $doctor->delete();

        return response()->json([
            'message' => 'Médico eliminado correctamente'
        ]);
    }

    public function import(Request $request)
    {
        $companyId = $this->getCompanyId();
        if (!$companyId) {
            return response()->json(['error' => 'Compañía no identificada'], 400);
        }

        $rows = $request->input('rows', []);
        $updateExisting = (bool) $request->input('update_existing', true);

        if (!is_array($rows) || empty($rows)) {
            return response()->json(['error' => 'No se proporcionaron filas para importar'], 422);
        }

        $imported = 0;
        $updated = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $lineNum = $i + 1;
            $nombre = trim($row['nombre_apellido'] ?? '');
            $nit = trim($row['nit_documento'] ?? '');

            if (empty($nombre)) {
                $errors[] = "Línea {$lineNum}: El nombre y apellido es obligatorio.";
                continue;
            }
            if (empty($nit)) {
                $errors[] = "Línea {$lineNum}: El NIT o documento es obligatorio.";
                continue;
            }

            $existing = FelDoctor::byCompany($companyId)
                ->where('nit_documento', $nit)
                ->first();

            if ($existing) {
                if ($updateExisting) {
                    $existing->update([
                        'nombre_apellido' => $nombre,
                        'nro_matricula' => trim($row['nro_matricula'] ?? '') ?: $existing->nro_matricula,
                        'especialidad' => trim($row['especialidad'] ?? '') ?: $existing->especialidad,
                        'telefono' => trim($row['telefono'] ?? '') ?: $existing->telefono,
                        'email' => trim($row['email'] ?? '') ?: $existing->email,
                    ]);
                    $updated++;
                }
            } else {
                FelDoctor::create([
                    'company_id' => $companyId,
                    'nombre_apellido' => $nombre,
                    'nit_documento' => $nit,
                    'nro_matricula' => trim($row['nro_matricula'] ?? '') ?: null,
                    'especialidad' => trim($row['especialidad'] ?? '') ?: 'Medicina General',
                    'telefono' => trim($row['telefono'] ?? '') ?: null,
                    'email' => trim($row['email'] ?? '') ?: null,
                    'activo' => true,
                ]);
                $imported++;
            }
        }

        return response()->json([
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
        ]);
    }

    public function getKardex(Request $request, $id)
    {
        $companyId = $this->getCompanyId();
        
        $summary = FelDoctorKardexSummary::where('company_id', $companyId)
                                         ->where('doctor_id', $id)
                                         ->first();

        $query = FelDoctorKardexMovement::where('company_id', $companyId)
                                        ->where('doctor_id', $id)
                                        ->orderBy('invoice_date', 'desc');

        $search = $request->query('search', '');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%")
                  ->orWhere('procedimiento', 'like', "%{$search}%")
                  ->orWhere('nro_factura_medico', 'like', "%{$search}%");
            });
        }
        
        $nroQuirofano = $request->query('nro_quirofano', '');
        if (!empty($nroQuirofano)) {
            $query->where('nro_quirofano', 'like', "%{$nroQuirofano}%");
        }

        $perPage = $request->query('per_page', 15);
        $movements = $query->paginate((int) $perPage);

        return response()->json([
            'summary' => $summary ?: [
                'total_procedimientos' => 0,
                'total_monto_generado' => 0,
                'promedio_por_intervencion' => 0
            ],
            'movements' => $movements->items(),
            'meta' => [
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'per_page' => $movements->perPage(),
                'total' => $movements->total(),
            ]
        ]);
    }
}
