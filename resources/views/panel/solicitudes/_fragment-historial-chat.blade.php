@php
    use App\Domain\Enums\HistorialRespuestaCanal;

    $modo = $modo ?? 'consultor';
    $idPrefix = $idPrefix ?? 'hist';
    $entries = $solicitud->historialRespuestas->sortBy(
        fn ($h) => $h->fecha_respuesta?->getTimestamp() ?? 0,
    );

    $resolverCanal = static function ($h): string {
        if ($h->canal instanceof HistorialRespuestaCanal) {
            return $h->canal->value;
        }

        return trim((string) ($h->canal ?? HistorialRespuestaCanal::ClienteSj->value));
    };

    $nombreUsuario = static function ($h): string {
        $login = trim((string) ($h->usuario?->usuario ?? ''));
        $p = $h->usuario?->persona;
        $nombre = '';
        if ($p !== null) {
            $nombre = trim(implode(' ', array_filter([
                trim((string) ($p->nombre ?? '')),
                trim((string) ($p->paterno ?? '')),
                trim((string) ($p->materno ?? '')),
            ])));
        }
        if ($login !== '' && $nombre !== '') {
            return $login.' · '.$nombre;
        }

        return $login !== '' ? $login : ($nombre !== '' ? $nombre : '—');
    };

    $iniciales = static function ($h): string {
        $login = trim((string) ($h->usuario?->usuario ?? ''));

        return $login !== '' ? mb_strtoupper(mb_substr($login, 0, 2)) : 'SJ';
    };

    $huboCambioEstado = static function ($h): bool {
        $anterior = trim((string) ($h->estado_anterior ?? ''));
        $actual = trim((string) ($h->estado_actual ?? ''));

        if ($anterior === '' && $actual === '') {
            return false;
        }

        return $anterior !== $actual;
    };

    $etiquetaCanal = static function (string $canalVal): ?array {
        return match ($canalVal) {
            HistorialRespuestaCanal::ClienteSj->value => ['Cliente / SJ', 'cliente'],
            HistorialRespuestaCanal::SjProveedor->value => ['Operativo SJ ↔ asociado', 'operativo'],
            HistorialRespuestaCanal::SoloSj->value => ['Solo auditoría SJ', 'interno'],
            default => null,
        };
    };

    /** Separa cuerpo del mensaje y bloque «Comentario:» (registros ya guardados en BD). */
    $partirRespuestaHistorial = static function (string $raw): array {
        if (preg_match('/\n\s*Comentario:\s*\n/u', $raw, $m, PREG_OFFSET_CAPTURE)) {
            $pos = $m[0][1];
            $len = strlen($m[0][0]);

            return [
                'cuerpo' => trim(substr($raw, 0, $pos)),
                'comentario' => trim(substr($raw, $pos + $len)),
            ];
        }

        return ['cuerpo' => trim($raw), 'comentario' => null];
    };
@endphp

@once
    @push('styles')
        @php
            $histChatCss = public_path('css/legacy/historial-chat.css');
            $histChatV = is_file($histChatCss) ? (string) filemtime($histChatCss) : '1';
        @endphp
        <link rel="stylesheet" href="{{ asset('css/legacy/historial-chat.css') }}?v={{ $histChatV }}">
    @endpush
@endonce

@if ($entries->isEmpty())
    <p class="sj-hist-chat sj-hist-chat--empty text-muted small mb-0">Sin movimientos en el historial.</p>
@else
    <div class="sj-hist-chat" id="{{ $idPrefix }}-chat-thread" role="log" aria-label="Historial de la solicitud">
        @foreach ($entries as $h)
            @php
                $canalVal = $resolverCanal($h);
                $bubbleMod = match ($canalVal) {
                    HistorialRespuestaCanal::SjProveedor->value => 'operativo',
                    HistorialRespuestaCanal::SoloSj->value => 'interno',
                    default => 'cliente',
                };
                $canalInfo = in_array($modo, ['consultor', 'proveedor'], true) ? $etiquetaCanal($canalVal) : null;
                $asoc = trim((string) ($h->usuario?->proveedor?->nombre_comercial ?? $h->usuario?->proveedor?->razon_social_proveedor ?? ''));
            @endphp
            <article class="sj-hist-bubble sj-hist-bubble--{{ $bubbleMod }}" id="{{ $idPrefix }}-hist-{{ $h->id }}">
                <div class="sj-hist-bubble__avatar" aria-hidden="true">{{ $iniciales($h) }}</div>
                <div class="sj-hist-bubble__card">
                    <header class="sj-hist-bubble__head">
                        <span class="sj-hist-bubble__user">{{ $nombreUsuario($h) }}</span>
                        <span class="sj-hist-bubble__meta">
                            @if ($h->fecha_respuesta)
                                <time datetime="{{ $h->fecha_respuesta->toIso8601String() }}">
                                    {{ $h->fecha_respuesta->format('d/m/Y') }}
                                    <span class="sj-hist-bubble__meta-sep">·</span>
                                    {{ $h->fecha_respuesta->format('h:i A') }}
                                </time>
                            @else
                                —
                            @endif
                        </span>
                    </header>

                    @if ($canalInfo !== null)
                        <div class="sj-hist-bubble__badges">
                            <span class="sj-hist-bubble__badge-canal sj-hist-bubble__badge-canal--{{ $canalInfo[1] }}">
                                {{ $canalInfo[0] }}
                            </span>
                        </div>
                    @endif

                    @if ($huboCambioEstado($h))
                        <div class="sj-hist-bubble__estado">
                            <i class="fas fa-arrows-rotate me-1 text-muted" style="font-size:0.75rem" aria-hidden="true"></i>
                            Estado:
                            <strong>{{ $h->estado_anterior ?? '—' }}</strong>
                            <span class="sj-hist-bubble__estado-arrow" aria-hidden="true">→</span>
                            <strong>{{ $h->estado_actual ?? '—' }}</strong>
                        </div>
                    @endif

                    @php
                        $partesTexto = $partirRespuestaHistorial((string) ($h->respuesta ?? ''));
                    @endphp
                    <div class="sj-hist-bubble__texto-wrap">
                        @if ($partesTexto['cuerpo'] !== '')
                            <p class="sj-hist-bubble__texto">{!! nl2br(e($partesTexto['cuerpo']), false) !!}</p>
                        @endif
                        @if (($partesTexto['comentario'] ?? '') !== '')
                            <div class="sj-hist-bubble__comentario">
                                <span class="sj-hist-bubble__comentario-label">Comentario</span>
                                <p class="sj-hist-bubble__comentario-texto">{!! nl2br(e($partesTexto['comentario']), false) !!}</p>
                            </div>
                        @endif
                    </div>

                    @if ($modo === 'consultor' && $asoc !== '')
                        <div class="sj-hist-bubble__asoc">
                            <i class="fas fa-building me-1" aria-hidden="true"></i>Asociado: {{ $asoc }}
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif
