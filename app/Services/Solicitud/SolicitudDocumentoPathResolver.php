<?php

declare(strict_types=1);

namespace App\Services\Solicitud;

/**
 * Resuelve rutas almacenadas en BD (legado a menudo solo nombre: resp_*.pdf) contra
 * carpetas típicas (public/uploads, storage/app/public/uploads, LEGACY_DOCUMENTS_ROOT).
 */
final class SolicitudDocumentoPathResolver
{
    /**
     * @param  list<string>  $roots  Directorios absolutos existentes (sin barra final obligatoria).
     */
    public function __construct(
        private readonly array $roots,
    ) {}

    public function exists(?string $dbPath): bool
    {
        return $this->resolve($dbPath) !== null;
    }

    public function resolve(?string $dbPath): ?string
    {
        $norm = $this->normalizeRelative($dbPath ?? '');
        if ($norm === null) {
            return null;
        }

        foreach ($this->roots as $root) {
            $root = rtrim($root, DIRECTORY_SEPARATOR);
            foreach ($this->relativeVariants($norm) as $rel) {
                $relFs = str_replace('/', DIRECTORY_SEPARATOR, $rel);
                $full = $root.DIRECTORY_SEPARATOR.$relFs;
                if (is_string($full) && is_file($full)) {
                    return $full;
                }
            }
        }

        return null;
    }

    private function normalizeRelative(string $path): ?string
    {
        $path = trim(str_replace('\\', '/', $path));
        if ($path === '') {
            return null;
        }
        $path = preg_replace('#^\./+#', '', $path) ?? '';
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }
        if (preg_match('#^[a-zA-Z]:/#', $path) === 1) {
            return null;
        }

        return $path;
    }

    /**
     * @return list<string>
     */
    private function relativeVariants(string $norm): array
    {
        $variants = [$norm];
        if (! str_contains($norm, '/')) {
            foreach (['uploads', 'documentos', 'archivos', 'files', 'respuestas', 'documentos_respuesta', 'pdf'] as $sub) {
                $variants[] = $sub.'/'.$norm;
            }
        }

        return array_values(array_unique($variants));
    }
}
