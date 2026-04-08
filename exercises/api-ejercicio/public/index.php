<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API REST - Citas Médicas</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f3f4f6;
            --card: #ffffff;
            --ink: #0f172a;
            --muted: #475569;
            --accent: #0f766e;
            --line: #dbe4ea;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(180deg, #e8f1f0 0%, var(--bg) 45%, #eef2ff 100%);
            color: var(--ink);
        }

        main {
            max-width: 920px;
            margin: 0 auto;
            padding: 48px 20px 72px;
        }

        .hero {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(15, 118, 110, 0.12);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 18px 60px rgba(15, 23, 42, 0.08);
            backdrop-filter: blur(10px);
        }

        h1 {
            margin: 0 0 12px;
            font-size: clamp(2rem, 4vw, 3.6rem);
            letter-spacing: -0.03em;
        }

        p {
            margin: 0 0 24px;
            color: var(--muted);
            line-height: 1.6;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
            margin-top: 26px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
        }

        .card h2 {
            margin: 0 0 12px;
            font-size: 1.05rem;
        }

        code {
            display: inline-block;
            background: #e2e8f0;
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.95rem;
        }

        ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.7;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #d1fae5;
            color: #065f46;
            border-radius: 999px;
            padding: 8px 12px;
            font-weight: 700;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <main>
        <section class="hero">
            <div class="badge">API REST activa</div>
            <h1>Sistema de Gestión de Citas Médicas</h1>
            <p>La API está funcionando. Usa los endpoints de pacientes y citas para probar el CRUD desde tu cliente HTTP o los tests automáticos.</p>

            <div class="grid">
                <div class="card">
                    <h2>Pacientes</h2>
                    <ul>
                        <li><code>GET /api/pacientes</code></li>
                        <li><code>GET /api/pacientes/{id}</code></li>
                        <li><code>POST /api/pacientes</code></li>
                        <li><code>PUT /api/pacientes/{id}</code></li>
                        <li><code>DELETE /api/pacientes/{id}</code></li>
                    </ul>
                </div>

                <div class="card">
                    <h2>Citas</h2>
                    <ul>
                        <li><code>GET /api/citas</code></li>
                        <li><code>GET /api/citas/{id}</code></li>
                        <li><code>POST /api/citas</code></li>
                        <li><code>PUT /api/citas/{id}</code></li>
                        <li><code>DELETE /api/citas/{id}</code></li>
                    </ul>
                </div>
            </div>
        </section>
    </main>
</body>
</html>