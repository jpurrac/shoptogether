const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  Header, Footer, AlignmentType, HeadingLevel, BorderStyle, WidthType,
  ShadingType, VerticalAlign, PageNumber, LevelFormat, ExternalHyperlink,
  PageBreak
} = require('docx');
const fs = require('fs');

// ── Helpers ──────────────────────────────────────────────────────────────────
const W = 9360; // content width DXA (US Letter 1" margins)

const border  = { style: BorderStyle.SINGLE, size: 1, color: 'CCCCCC' };
const borders = { top: border, bottom: border, left: border, right: border };
const noBorder = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };
const noBorders = { top: noBorder, bottom: noBorder, left: noBorder, right: noBorder };

function h1(text) {
  return new Paragraph({ heading: HeadingLevel.HEADING_1, children: [new TextRun(text)] });
}
function h2(text) {
  return new Paragraph({ heading: HeadingLevel.HEADING_2, children: [new TextRun(text)] });
}
function h3(text) {
  return new Paragraph({ heading: HeadingLevel.HEADING_3, children: [new TextRun(text)] });
}
function p(text, opts = {}) {
  return new Paragraph({
    spacing: { after: 120 },
    children: [new TextRun({ text, ...opts })]
  });
}
function bullet(text, bold = false) {
  return new Paragraph({
    numbering: { reference: 'bullets', level: 0 },
    spacing: { after: 80 },
    children: [new TextRun({ text, bold })]
  });
}
function numbered(text) {
  return new Paragraph({
    numbering: { reference: 'numbers', level: 0 },
    spacing: { after: 80 },
    children: [new TextRun(text)]
  });
}
function code(text) {
  return new Paragraph({
    spacing: { after: 80 },
    indent: { left: 360 },
    children: [new TextRun({ text, font: 'Courier New', size: 18, color: '1565C0' })]
  });
}
function note(text) {
  return new Paragraph({
    spacing: { before: 80, after: 160 },
    indent: { left: 360 },
    border: { left: { style: BorderStyle.SINGLE, size: 6, color: 'F57C00', space: 8 } },
    children: [new TextRun({ text: '⚠ ' + text, color: 'E65100', size: 20 })]
  });
}
function tip(text) {
  return new Paragraph({
    spacing: { before: 80, after: 160 },
    indent: { left: 360 },
    border: { left: { style: BorderStyle.SINGLE, size: 6, color: '43A047', space: 8 } },
    children: [new TextRun({ text: '✓ ' + text, color: '2E7D32', size: 20 })]
  });
}
function space() {
  return new Paragraph({ spacing: { after: 160 }, children: [new TextRun('')] });
}
function pageBreak() {
  return new Paragraph({ children: [new PageBreak()] });
}

function twoColRow(label, value, labelW = 2800, valueW = 6560, header = false) {
  return new TableRow({
    children: [
      new TableCell({
        borders,
        width: { size: labelW, type: WidthType.DXA },
        shading: header
          ? { fill: '1565C0', type: ShadingType.CLEAR }
          : { fill: 'F4F6FA', type: ShadingType.CLEAR },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({
          children: [new TextRun({
            text: label,
            bold: true,
            color: header ? 'FFFFFF' : '1A2332',
            size: 18
          })]
        })]
      }),
      new TableCell({
        borders,
        width: { size: valueW, type: WidthType.DXA },
        shading: header
          ? { fill: '1565C0', type: ShadingType.CLEAR }
          : { fill: 'FFFFFF', type: ShadingType.CLEAR },
        margins: { top: 80, bottom: 80, left: 120, right: 120 },
        children: [new Paragraph({
          children: [new TextRun({
            text: value,
            color: header ? 'FFFFFF' : '1A2332',
            size: 18
          })]
        })]
      }),
    ]
  });
}

function envTable(rows) {
  return new Table({
    width: { size: W, type: WidthType.DXA },
    columnWidths: [3200, 6160],
    rows: [
      twoColRow('Variable', 'Valor', 3200, 6160, true),
      ...rows.map(([k, v]) => twoColRow(k, v, 3200, 6160))
    ]
  });
}

function stepsTable(steps) {
  return new Table({
    width: { size: W, type: WidthType.DXA },
    columnWidths: [600, 8760],
    rows: steps.map(([num, text], i) => new TableRow({
      children: [
        new TableCell({
          borders,
          width: { size: 600, type: WidthType.DXA },
          shading: { fill: '1565C0', type: ShadingType.CLEAR },
          margins: { top: 80, bottom: 80, left: 100, right: 100 },
          verticalAlign: VerticalAlign.CENTER,
          children: [new Paragraph({
            alignment: AlignmentType.CENTER,
            children: [new TextRun({ text: String(num), bold: true, color: 'FFFFFF', size: 20 })]
          })]
        }),
        new TableCell({
          borders,
          width: { size: 8760, type: WidthType.DXA },
          margins: { top: 80, bottom: 80, left: 160, right: 120 },
          children: [new Paragraph({ children: [new TextRun({ text, size: 20 })] })]
        }),
      ]
    }))
  });
}

// ── Document ─────────────────────────────────────────────────────────────────
const doc = new Document({
  numbering: {
    config: [
      {
        reference: 'bullets',
        levels: [{ level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
      },
      {
        reference: 'numbers',
        levels: [{ level: 0, format: LevelFormat.DECIMAL, text: '%1.', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } }]
      },
    ]
  },
  styles: {
    default: { document: { run: { font: 'Arial', size: 20 } } },
    paragraphStyles: [
      { id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 36, bold: true, font: 'Arial', color: '1D3461' },
        paragraph: { spacing: { before: 400, after: 160 }, outlineLevel: 0,
          border: { bottom: { style: BorderStyle.SINGLE, size: 4, color: '00ACC1', space: 4 } } } },
      { id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 26, bold: true, font: 'Arial', color: '1565C0' },
        paragraph: { spacing: { before: 280, after: 120 }, outlineLevel: 1 } },
      { id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 22, bold: true, font: 'Arial', color: '00838F' },
        paragraph: { spacing: { before: 200, after: 80 }, outlineLevel: 2 } },
    ]
  },
  sections: [{
    properties: {
      page: {
        size: { width: 12240, height: 15840 },
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 }
      }
    },
    headers: {
      default: new Header({
        children: [new Paragraph({
          tabStops: [{ type: 'right', position: 9360 }],
          border: { bottom: { style: BorderStyle.SINGLE, size: 3, color: 'E8ECF2', space: 6 } },
          children: [
            new TextRun({ text: 'ShopTogether — Guia de Deploy', color: '9AABBB', size: 16 }),
            new TextRun({ text: '\t', size: 16 }),
            new TextRun({ text: 'HostGator Business Plan', color: '9AABBB', size: 16 }),
          ]
        })]
      })
    },
    footers: {
      default: new Footer({
        children: [new Paragraph({
          alignment: AlignmentType.CENTER,
          border: { top: { style: BorderStyle.SINGLE, size: 3, color: 'E8ECF2', space: 6 } },
          children: [
            new TextRun({ text: 'Pagina ', color: '9AABBB', size: 16 }),
            new TextRun({ children: [PageNumber.CURRENT], color: '9AABBB', size: 16 }),
            new TextRun({ text: ' de ', color: '9AABBB', size: 16 }),
            new TextRun({ children: [PageNumber.TOTAL_PAGES], color: '9AABBB', size: 16 }),
          ]
        })]
      })
    },
    children: [

      // ── PORTADA ────────────────────────────────────────────────────────────
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { before: 1440, after: 160 },
        children: [new TextRun({ text: 'ShopTogether', font: 'Arial', size: 72, bold: true, color: '1D3461' })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 80 },
        children: [new TextRun({ text: 'Guia de Deploy a HostGator', font: 'Arial', size: 32, color: '00ACC1' })]
      }),
      new Paragraph({
        alignment: AlignmentType.CENTER,
        spacing: { after: 800 },
        children: [new TextRun({ text: 'Business Plan — Junio 2026', font: 'Arial', size: 22, color: '9AABBB' })]
      }),
      new Table({
        width: { size: W, type: WidthType.DXA },
        columnWidths: [4680, 4680],
        rows: [new TableRow({
          children: [
            new TableCell({
              borders: noBorders,
              width: { size: 4680, type: WidthType.DXA },
              shading: { fill: 'EFF6FF', type: ShadingType.CLEAR },
              margins: { top: 160, bottom: 160, left: 200, right: 200 },
              children: [
                new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ text: 'shop.jpurrac.cl', bold: true, color: '1565C0', size: 22 })] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 0 }, children: [new TextRun({ text: 'Frontend Vue 3', color: '5A6B82', size: 18 })] }),
              ]
            }),
            new TableCell({
              borders: noBorders,
              width: { size: 4680, type: WidthType.DXA },
              shading: { fill: 'E8F5E9', type: ShadingType.CLEAR },
              margins: { top: 160, bottom: 160, left: 200, right: 200 },
              children: [
                new Paragraph({ alignment: AlignmentType.CENTER, children: [new TextRun({ text: 'api-shop.jpurrac.cl', bold: true, color: '2E7D32', size: 22 })] }),
                new Paragraph({ alignment: AlignmentType.CENTER, spacing: { after: 0 }, children: [new TextRun({ text: 'Backend Laravel 12', color: '5A6B82', size: 18 })] }),
              ]
            }),
          ]
        })]
      }),
      pageBreak(),

      // ── 1. REQUISITOS PREVIOS ───────────────────────────────────────────────
      h1('1. Requisitos Previos'),
      p('Antes de iniciar el deploy, verificar que se cuenta con:'),
      space(),
      bullet('Cuenta HostGator Business Plan activa'),
      bullet('Dominio jpurrac.cl apuntando a HostGator'),
      bullet('Acceso a cPanel y SSH habilitado'),
      bullet('Node.js 20+ instalado en la maquina local (para el build del frontend)'),
      bullet('Composer instalado en local (para verificar dependencias)'),
      bullet('Acceso al repositorio del proyecto'),
      space(),
      note('HostGator Business Plan incluye SSH. Verificar en cPanel > SSH Access que este habilitado.'),
      space(),

      // ── 2. SUBDOMINOS ───────────────────────────────────────────────────────
      h1('2. Crear Subdominios en cPanel'),
      p('En cPanel ir a Domains > Subdomains y crear los dos subdominios:'),
      space(),
      new Table({
        width: { size: W, type: WidthType.DXA },
        columnWidths: [3000, 6360],
        rows: [
          twoColRow('Subdominio', 'Document Root', 3000, 6360, true),
          twoColRow('shop.jpurrac.cl', '/public_html/shop', 3000, 6360),
          twoColRow('api-shop.jpurrac.cl', '/public_html/api-shop/public', 3000, 6360),
        ]
      }),
      space(),
      note('El document root del backend apunta a la carpeta /public de Laravel, NO a la raiz del proyecto.'),
      space(),

      // ── 3. BASE DE DATOS ────────────────────────────────────────────────────
      h1('3. Crear Base de Datos MySQL'),
      p('En cPanel ir a Databases > MySQL Databases:'),
      space(),
      stepsTable([
        [1, 'Crear nueva base de datos: shoptogether (el prefijo de usuario se agrega automaticamente, ej: jpurrac_shoptogether)'],
        [2, 'Crear usuario de base de datos con contrasena segura'],
        [3, 'Asignar el usuario a la base de datos con TODOS LOS PRIVILEGIOS'],
        [4, 'Anotar: nombre de BD, usuario y contrasena para el .env del backend'],
      ]),
      space(),

      // ── 4. BACKEND ──────────────────────────────────────────────────────────
      pageBreak(),
      h1('4. Deploy del Backend (Laravel)'),

      h2('4.1 Subir archivos'),
      p('Subir la carpeta backend/ al servidor. Usar FTP (FileZilla) o Git via SSH:'),
      space(),
      p('Opcion A — Via SSH (recomendado):'),
      code('ssh usuario@jpurrac.cl'),
      code('cd ~/public_html'),
      code('git clone https://github.com/tu-usuario/shoptogether.git temp-shop'),
      code('cp -r temp-shop/backend api-shop'),
      code('rm -rf temp-shop'),
      space(),
      p('Opcion B — Via FTP:'),
      bullet('Subir todo el contenido de la carpeta backend/ a /public_html/api-shop/'),
      bullet('NO subir la carpeta vendor/ (se instala con Composer en el servidor)'),
      space(),

      h2('4.2 Instalar dependencias via SSH'),
      code('cd ~/public_html/api-shop'),
      code('php8.3 -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"'),
      code('php8.3 composer-setup.php'),
      code('php8.3 composer.phar install --no-dev --optimize-autoloader'),
      space(),
      note('Usar php8.3 (o la version disponible en HostGator). Verificar con: php -v'),
      space(),

      h2('4.3 Configurar .env de produccion'),
      p('Crear el archivo .env en /public_html/api-shop/:'),
      code('cp .env.example .env'),
      code('nano .env'),
      space(),
      p('Valores a configurar:'),
      envTable([
        ['APP_ENV', 'production'],
        ['APP_DEBUG', 'false'],
        ['APP_URL', 'https://api-shop.jpurrac.cl'],
        ['DB_HOST', 'localhost'],
        ['DB_DATABASE', 'jpurrac_shoptogether'],
        ['DB_USERNAME', 'jpurrac_dbuser'],
        ['DB_PASSWORD', 'tu_contrasena_segura'],
        ['FRONTEND_URL', 'https://shop.jpurrac.cl'],
        ['BROADCAST_CONNECTION', 'log'],
        ['SESSION_DRIVER', 'file'],
        ['CACHE_STORE', 'file'],
      ]),
      space(),
      note('BROADCAST_CONNECTION=log desactiva el socket en produccion. El frontend usara polling automaticamente.'),
      space(),

      h2('4.4 Generar APP_KEY y migrar'),
      code('php8.3 artisan key:generate'),
      code('php8.3 artisan migrate --force'),
      code('php8.3 artisan optimize'),
      space(),

      h2('4.5 Permisos de carpetas'),
      code('chmod -R 775 storage bootstrap/cache'),
      code('chown -R :nobody storage bootstrap/cache'),
      space(),

      h2('4.6 Configurar .htaccess'),
      p('Crear /public_html/api-shop/public/.htaccess con el siguiente contenido:'),
      code('<IfModule mod_rewrite.c>'),
      code('  RewriteEngine On'),
      code('  RewriteCond %{REQUEST_FILENAME} !-f'),
      code('  RewriteCond %{REQUEST_FILENAME} !-d'),
      code('  RewriteRule ^ index.php [L]'),
      code('</IfModule>'),
      space(),
      p('Tambien agregar /public_html/api-shop/.htaccess para proteger la raiz:'),
      code('<IfModule mod_rewrite.c>'),
      code('  RewriteEngine On'),
      code('  RewriteRule ^(.*)$ public/$1 [L]'),
      code('</IfModule>'),
      space(),

      // ── 5. FRONTEND ─────────────────────────────────────────────────────────
      pageBreak(),
      h1('5. Deploy del Frontend (Vue 3)'),

      h2('5.1 Build local'),
      p('Desde la maquina local, en la carpeta frontend/:'),
      space(),
      p('El archivo frontend/.env.production ya esta configurado con:'),
      envTable([
        ['VITE_API_URL', 'https://api-shop.jpurrac.cl/api'],
        ['VITE_APP_NAME', 'ShopTogether'],
        ['VITE_SOCKET_ENABLED', 'false'],
      ]),
      space(),
      p('Ejecutar el build:'),
      code('cd frontend'),
      code('npm install'),
      code('npm run build'),
      space(),
      p('Esto genera la carpeta dist/ con todos los archivos estaticos optimizados.'),
      space(),

      h2('5.2 Subir archivos'),
      p('Subir el CONTENIDO de la carpeta dist/ (no la carpeta dist/ misma) a /public_html/shop/ via FTP o SSH.'),
      space(),
      stepsTable([
        [1, 'Abrir FileZilla u otro cliente FTP'],
        [2, 'Conectar al servidor HostGator con las credenciales FTP de cPanel'],
        [3, 'Navegar a /public_html/shop/ en el servidor'],
        [4, 'Subir TODO el contenido de la carpeta frontend/dist/ al directorio shop/'],
      ]),
      space(),

      h2('5.3 Configurar .htaccess para SPA'),
      p('Crear /public_html/shop/.htaccess para que Vue Router funcione correctamente:'),
      code('<IfModule mod_rewrite.c>'),
      code('  Options -MultiViews'),
      code('  RewriteEngine On'),
      code('  RewriteBase /'),
      code('  RewriteCond %{REQUEST_FILENAME} !-f'),
      code('  RewriteCond %{REQUEST_FILENAME} !-d'),
      code('  RewriteRule ^ index.html [QSA,L]'),
      code('</IfModule>'),
      space(),
      note('Sin este .htaccess, al recargar la pagina en rutas como /lists o /stats dara error 404.'),
      space(),

      // ── 6. SSL ──────────────────────────────────────────────────────────────
      h1('6. Activar SSL (HTTPS)'),
      p('En cPanel ir a Security > SSL/TLS Status:'),
      space(),
      bullet('Seleccionar ambos subdominios: shop.jpurrac.cl y api-shop.jpurrac.cl'),
      bullet('Hacer clic en "Run AutoSSL"'),
      bullet('Esperar 5-10 minutos hasta que los certificados queden activos'),
      space(),
      tip('HostGator Business Plan incluye SSL gratuito con AutoSSL. No se necesita pagar certificados adicionales.'),
      space(),
      p('Agregar redireccion HTTPS en /public_html/api-shop/public/.htaccess:'),
      code('<IfModule mod_rewrite.c>'),
      code('  RewriteCond %{HTTPS} off'),
      code('  RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]'),
      code('</IfModule>'),
      space(),

      // ── 7. CORS ─────────────────────────────────────────────────────────────
      h1('7. Verificar CORS'),
      p('El archivo backend/config/cors.php debe incluir el dominio de produccion en allowed_origins:'),
      code("'allowed_origins' => ["),
      code("    'https://shop.jpurrac.cl',"),
      code("    'http://localhost:5173',  // desarrollo local"),
      code("],"),
      space(),
      p('Tambien verificar que FRONTEND_URL en el .env del servidor sea https://shop.jpurrac.cl'),
      space(),

      // ── 8. POLLING ──────────────────────────────────────────────────────────
      pageBreak(),
      h1('8. Comportamiento en Produccion (Sin WebSocket)'),
      p('HostGator shared hosting no soporta procesos persistentes como Laravel Reverb. El proyecto esta configurado para usar polling automaticamente.'),
      space(),
      new Table({
        width: { size: W, type: WidthType.DXA },
        columnWidths: [4680, 4680],
        rows: [
          new TableRow({
            children: [
              new TableCell({
                borders,
                width: { size: 4680, type: WidthType.DXA },
                shading: { fill: 'E3F2FD', type: ShadingType.CLEAR },
                margins: { top: 120, bottom: 120, left: 160, right: 160 },
                children: [
                  new Paragraph({ children: [new TextRun({ text: 'Desarrollo (local)', bold: true, color: '1565C0', size: 22 })] }),
                  new Paragraph({ spacing: { after: 0 }, children: [new TextRun({ text: 'VITE_SOCKET_ENABLED=true', font: 'Courier New', size: 18, color: '1565C0' })] }),
                  new Paragraph({ spacing: { after: 0 }, children: [new TextRun({ text: 'Usa Laravel Reverb (WebSocket tiempo real)', size: 18, color: '5A6B82' })] }),
                ]
              }),
              new TableCell({
                borders,
                width: { size: 4680, type: WidthType.DXA },
                shading: { fill: 'E8F5E9', type: ShadingType.CLEAR },
                margins: { top: 120, bottom: 120, left: 160, right: 160 },
                children: [
                  new Paragraph({ children: [new TextRun({ text: 'Produccion (HostGator)', bold: true, color: '2E7D32', size: 22 })] }),
                  new Paragraph({ spacing: { after: 0 }, children: [new TextRun({ text: 'VITE_SOCKET_ENABLED=false', font: 'Courier New', size: 18, color: '43A047' })] }),
                  new Paragraph({ spacing: { after: 0 }, children: [new TextRun({ text: 'Usa polling cada 10 segundos (solo listas compartidas)', size: 18, color: '5A6B82' })] }),
                ]
              }),
            ]
          })
        ]
      }),
      space(),
      tip('El polling solo se activa cuando la lista tiene mas de 1 miembro. Listas personales no hacen peticiones extra.'),
      space(),

      // ── 9. VERIFICACION ─────────────────────────────────────────────────────
      h1('9. Verificacion Post-Deploy'),
      p('Checklist para confirmar que todo funciona:'),
      space(),
      stepsTable([
        [1, 'Abrir https://shop.jpurrac.cl y verificar que carga la pantalla de login'],
        [2, 'Iniciar sesion con un usuario existente'],
        [3, 'Crear una nueva lista de compras'],
        [4, 'Agregar productos a la lista'],
        [5, 'Marcar productos como comprados (toggle)'],
        [6, 'Probar el codigo de acceso: unirse a una lista con otro usuario'],
        [7, 'Verificar que el polling actualiza los cambios en ~10 segundos'],
        [8, 'Cerrar una lista y verificar que aparece en Estadisticas'],
        [9, 'Verificar HTTPS activo en ambos subdominios'],
        [10, 'Verificar que el API responde: https://api-shop.jpurrac.cl/api/auth/me'],
      ]),
      space(),

      // ── 10. COMANDOS UTILES ─────────────────────────────────────────────────
      pageBreak(),
      h1('10. Comandos Utiles por SSH'),
      space(),
      h2('Ver logs de Laravel'),
      code('tail -f ~/public_html/api-shop/storage/logs/laravel.log'),
      space(),
      h2('Limpiar cache despues de cambios'),
      code('cd ~/public_html/api-shop'),
      code('php8.3 artisan optimize:clear'),
      code('php8.3 artisan optimize'),
      space(),
      h2('Actualizar codigo (pull desde Git)'),
      code('cd ~/public_html/api-shop'),
      code('git pull origin main'),
      code('php8.3 composer.phar install --no-dev'),
      code('php8.3 artisan migrate --force'),
      code('php8.3 artisan optimize'),
      space(),
      h2('Actualizar frontend'),
      p('En maquina local:'),
      code('cd frontend && npm run build'),
      p('Luego subir el contenido de dist/ via FTP a /public_html/shop/'),
      space(),

      // ── 11. SOLUCION DE PROBLEMAS ───────────────────────────────────────────
      h1('11. Solucion de Problemas Comunes'),
      space(),
      new Table({
        width: { size: W, type: WidthType.DXA },
        columnWidths: [3800, 5560],
        rows: [
          twoColRow('Problema', 'Solucion', 3800, 5560, true),
          twoColRow('500 Internal Server Error', 'Revisar storage/logs/laravel.log. Verificar permisos de storage/ con chmod -R 775', 3800, 5560),
          twoColRow('CORS error en el browser', 'Verificar allowed_origins en config/cors.php incluye https://shop.jpurrac.cl', 3800, 5560),
          twoColRow('Error al recargar pagina (404)', 'Verificar que el .htaccess de Vue SPA esta en /public_html/shop/', 3800, 5560),
          twoColRow('Migraciones fallan', 'Verificar credenciales de BD en .env. Probar conexion con: php8.3 artisan db:show', 3800, 5560),
          twoColRow('Login no funciona (401)', 'Verificar APP_KEY generado. Ejecutar: php8.3 artisan key:generate', 3800, 5560),
          twoColRow('Imagenes o assets no cargan', 'Verificar que dist/assets/ fue subido completo a /public_html/shop/assets/', 3800, 5560),
        ]
      }),
      space(),

      // ── NOTAS FINALES ───────────────────────────────────────────────────────
      h1('Notas Finales'),
      space(),
      p('Estructura final en el servidor:'),
      code('/public_html/'),
      code('  shop/              <- Frontend Vue (archivos estaticos)'),
      code('    index.html'),
      code('    assets/'),
      code('    .htaccess'),
      code('  api-shop/          <- Backend Laravel'),
      code('    app/'),
      code('    config/'),
      code('    public/          <- Document root del subdominio'),
      code('    storage/'),
      code('    .env'),
      space(),
      tip('Para un futuro upgrade con WebSocket real: contratar un VPS (Hetzner CX11 ~3 EUR/mes) y desplegar solo el servicio Reverb ahi. Cambiar VITE_SOCKET_ENABLED=true y apuntar VITE_REVERB_HOST al VPS.'),
    ]
  }]
});

Packer.toBuffer(doc).then(buffer => {
  fs.writeFileSync('ShopTogether-Deploy-Guide.docx', buffer);
  console.log('Documento generado: ShopTogether-Deploy-Guide.docx');
});
