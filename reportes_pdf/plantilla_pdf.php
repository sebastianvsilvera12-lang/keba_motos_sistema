<?php
// ==============================================================
// PLANTILLA HTML PARA GENERAR REPORTES IMPRESOS / PDF
// ==============================================================

function renderizarPlantillaPDF($titulo, $contenidoHtml) {
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($titulo) . '</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; }
            .header { display: flex; justify-content: space-between; border-bottom: 2px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
            .logo { font-size: 20px; font-weight: bold; color: #0b1329; }
            .logo span { color: #2563eb; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; }
            th { background-color: #0b1329; color: #fff; padding: 8px; text-align: left; }
            td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
            .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #64748b; }
        </style>
    </head>
    <body>
        <div class="header">
            <div class="logo"><img src="' . BASE_URL . 'assets/img/logo_keba.png" alt="KEBA MOTOS" style="max-height:42px; vertical-align:middle;"></div>
            <div><strong>' . htmlspecialchars($titulo) . '</strong><br>Fecha: ' . date('d/m/Y') . '</div>
        </div>
        ' . $contenidoHtml . '
        <div class="footer">Sistema de Gestión de Mototaxis - KEBA MOTOS &copy; ' . date('Y') . '</div>
    </body>
    </html>';
}

