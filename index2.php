<?php
// Veritabanı Bağlantısı
$baglanti = new mysqli("localhost", "root", "", "urun_yonetim");
$baglanti->set_charset("utf8");
if ($baglanti->connect_error) {
    die("Bağlantı hatası: " . $baglanti->connect_error);
}

// AJAX İşlemleri
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    $action = $_POST['action'];

    // Dashboard istatistikleri
    if ($action === 'stats') {
        $totalProd = $baglanti->query("SELECT COUNT(*) AS cnt FROM urunler")->fetch_assoc()['cnt'];
        $totalStock = $baglanti->query("SELECT SUM(kalan_stok) AS sum FROM urunler")->fetch_assoc()['sum'];
        $top = $baglanti->query("SELECT ad, kalan_stok FROM urunler ORDER BY kalan_stok DESC LIMIT 5");
        $topData = [];
        while ($r = $top->fetch_assoc()) {
            $topData[] = ['ad'=>htmlspecialchars($r['ad']), 'kalan'=>floatval($r['kalan_stok'])];
        }
        echo json_encode([
            'status'=>'success',
            'totalProd'=>intval($totalProd),
            'totalStock'=>floatval($totalStock),
            'topProducts'=>$topData
        ]);
        exit;
    }

    // Ürün Listesi
    if ($action === 'prod_list') {
        $q = $baglanti->real_escape_string($_POST['q'] ?? '');
        $res = $baglanti->query("
            SELECT id,ad,aciklama,birim,kalan_stok 
            FROM urunler
            WHERE ad LIKE '%$q%' OR aciklama LIKE '%$q%' OR birim LIKE '%$q%'
            ORDER BY id DESC
        ");
        $data = [];
        while ($row = $res->fetch_assoc()) {
            $data[] = [
                'id'=>intval($row['id']),
                'ad'=>htmlspecialchars($row['ad']),
                'aciklama'=>htmlspecialchars($row['aciklama']),
                'birim'=>htmlspecialchars($row['birim']),
                'kalan'=>floatval($row['kalan_stok'])
            ];
        }
        echo json_encode(['status'=>'success','data'=>$data]);
        exit;
    }

    // Ürün CRUD
    if (in_array($action, ['prod_add','prod_edit','prod_delete'])) {
        $id = intval($_POST['id'] ?? 0);
        $ad = trim($_POST['ad'] ?? '');
        $aciklama = trim($_POST['aciklama'] ?? '');
        $birim = trim($_POST['birim'] ?? '');
        if ($action !== 'prod_delete' && ($ad === '' || !in_array($birim,['adet','kg']))) {
            echo json_encode(['status'=>'error','message'=>'Geçerli ürün adı ve birim girin.']);
            exit;
        }
        if ($action === 'prod_add') {
            $stmt = $baglanti->prepare("INSERT INTO urunler(ad,aciklama,birim) VALUES(?,?,?)");
            $stmt->bind_param("sss",$ad,$aciklama,$birim);
            $stmt->execute();
            $msg = 'Ürün eklendi';
        } elseif ($action === 'prod_edit') {
            if ($id<1) { echo json_encode(['status'=>'error','message'=>'Geçersiz ID']); exit; }
            $stmt = $baglanti->prepare("UPDATE urunler SET ad=?,aciklama=?,birim=? WHERE id=?");
            $stmt->bind_param("sssi",$ad,$aciklama,$birim,$id);
            $stmt->execute();
            $msg = 'Ürün güncellendi';
        } else { // prod_delete
            if ($id<1) { echo json_encode(['status'=>'error','message'=>'Geçersiz ID']); exit; }
            $baglanti->query("DELETE FROM urunler WHERE id=$id");
            $msg = 'Ürün silindi';
        }
        echo json_encode(['status'=>'success','message'=>$msg]);
        exit;
    }

    // Ürün Seçenekleri
    if ($action === 'prod_options') {
        $res = $baglanti->query("SELECT id,ad FROM urunler ORDER BY ad");
        $opts = ['<option value=\"\">Ürün seçin</option>'];
        while ($r = $res->fetch_assoc()) {
            $opts[] = '<option value="'.$r['id'].'">'.htmlspecialchars($r['ad']).'</option>';
        }
        echo json_encode(['status'=>'success','options'=>implode('',$opts)]);
        exit;
    }

    // Stok Listesi
    if ($action === 'stok_list') {
        $start = $_POST['start'] ?? '';
        $end   = $_POST['end']   ?? '';
        $conds = [];
        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/',$start)) {
            $d = DateTime::createFromFormat('d.m.Y',$start);
            $conds[] = "sh.tarih >= '".$d->format('Y-m-d')." 00:00:00'";
        }
        if (preg_match('/^\d{2}\.\d{2}\.\d{4}$/',$end)) {
            $d = DateTime::createFromFormat('d.m.Y',$end);
            $conds[] = "sh.tarih <= '".$d->format('Y-m-d')." 23:59:59'";
        }
        $where = $conds ? 'WHERE '.implode(' AND ',$conds) : '';
        $res = $baglanti->query("
            SELECT sh.id,
                   DATE_FORMAT(sh.tarih,'%d.%m.%Y %H:%i') AS tarih,
                   sh.islem_turu,miktar,
                   IFNULL(sh.stokAciklama,'') AS stokAciklama,
                   u.ad AS urun
            FROM stok_hareketleri sh
            JOIN urunler u ON u.id=sh.urun_id
            $where
            ORDER BY sh.tarih DESC
        ");
        $rows = [];
        while ($r = $res->fetch_assoc()) {
            $rows[] = $r;
        }
        echo json_encode(['status'=>'success','data'=>$rows]);
        exit;
    }

    // Stok CRUD
    if (in_array($action,['stok_add','stok_delete'])) {
        if ($action === 'stok_add') {
            $urun_id  = intval($_POST['urun_id']);
            $tip      = $_POST['tip']==='giris'?'giris':'cikis';
            $miktar   = floatval($_POST['miktar']);
            $aciklama = trim($_POST['aciklama']);
            if ($urun_id<1||$miktar<=0) {
                echo json_encode(['status'=>'error','message'=>'Geçersiz giriş']); exit;
            }
            $cur = floatval($baglanti->query("SELECT kalan_stok FROM urunler WHERE id=$urun_id")->fetch_assoc()['kalan_stok']);
            $new = $tip==='giris' ? $cur+$miktar : max(0,$cur-$miktar);
            $baglanti->query("UPDATE urunler SET kalan_stok=$new WHERE id=$urun_id");
            $stmt = $baglanti->prepare("
                INSERT INTO stok_hareketleri(urun_id,islem_turu,miktar,stokAciklama)
                VALUES(?,?,?,?)
            ");
            $stmt->bind_param("isds",$urun_id,$tip,$miktar,$aciklama);
            $stmt->execute();
            $msg = 'Stok işlemi kaydedildi';
        } else {
            $id = intval($_POST['id']);
            $row = $baglanti->query("SELECT urun_id,islem_turu,miktar FROM stok_hareketleri WHERE id=$id")->fetch_assoc();
            if ($row) {
                $cur = floatval($baglanti->query("SELECT kalan_stok FROM urunler WHERE id={$row['urun_id']}")->fetch_assoc()['kalan_stok']);
                $new = $row['islem_turu']==='giris' ? max(0,$cur-$row['miktar']) : $cur+$row['miktar'];
                $baglanti->query("UPDATE urunler SET kalan_stok=$new WHERE id={$row['urun_id']}");
                $baglanti->query("DELETE FROM stok_hareketleri WHERE id=$id");
            }
            $msg = 'Hareket silindi';
        }
        echo json_encode(['status'=>'success','message'=>$msg]);
        exit;
    }

    echo json_encode(['status'=>'error','message'=>'Bilinmeyen işlem']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yönetim Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="p-4">
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Yönetim</a>
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link active" id="navDashboard" href="#">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" id="navUrun" href="#">Ürünler</a></li>
            <li class="nav-item"><a class="nav-link" id="navStok" href="#">Stok Hareketleri</a></li>
        </ul>
    </div>
</nav>

<div class="container" style="margin-top:80px;">
    <!-- Dashboard -->
    <div id="pageDashboard">
        <div class="row mb-4">
            <div class="col-md-6"><div class="card text-white bg-primary"><div class="card-body">
                        <h5 class="card-title">Toplam Ürün</h5>
                        <p class="card-text display-6" id="statTotalProd">0</p>
                    </div></div></div>
            <div class="col-md-6"><div class="card text-white bg-success"><div class="card-body">
                        <h5 class="card-title">Toplam Stok</h5>
                        <p class="card-text display-6" id="statTotalStock">0</p>
                    </div></div></div>
        </div>
        <div class="card"><div class="card-body">
                <h5 class="card-title">En Çok Stoklu Ürünler</h5>
                <canvas id="chartTopProducts" height="100"></canvas>
            </div></div>
    </div>

    <!-- Ürünler -->
    <div id="pageUrun" style="display:none;">
        <div class="d-flex justify-content-between mb-3">
            <input type="text" id="searchProd" class="form-control w-50" placeholder="Ara...">
            <button class="btn btn-success" id="btnOpenProdModal">Yeni Ürün</button>
        </div>
        <table id="tblProd" class="table table-striped" style="width:100%">
            <thead>
            <tr><th>ID</th><th>Ad</th><th>Açıklama</th><th>Birim</th><th>Kalan</th><th>İşlem</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Stok Hareketleri -->
    <div id="pageStok" style="display:none;">
        <div class="row mb-3">
            <div class="col-md-4"><input type="text" id="stokStart" class="form-control" placeholder="Başlangıç (gg.aa.yyyy)"></div>
            <div class="col-md-4"><input type="text" id="stokEnd" class="form-control" placeholder="Bitiş (gg.aa.yyyy)"></div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary me-2" id="btnFilterStok">Filtrele</button>
                <button class="btn btn-success" id="btnOpenStokModal">Stok İşlemi</button>
            </div>
        </div>
        <table id="tblStok" class="table table-striped" style="width:100%">
            <thead>
            <tr><th>Tarih</th><th>Tür</th><th>Ürün</th><th>Miktar</th><th>Açıklama</th><th>İşlem</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modaller -->
<div class="modal fade" id="modalProd" tabindex="-1"><div class="modal-dialog"><div class="modal-content p-3">
            <h5>Ürün Ekle / Düzenle</h5>
            <input id="inpProdId" type="hidden">
            <input id="inpProdAd" class="form-control mb-2" placeholder="Ürün Adı">
            <input id="inpProdAciklama" class="form-control mb-2" placeholder="Açıklama">
            <select id="selProdBirim" class="form-select mb-2">
                <option value="">Birim seçin</option>
                <option value="adet">adet</option>
                <option value="kg">kg</option>
            </select>
            <div class="text-end">
                <button class="btn btn-secondary me-2" data-bs-dismiss="modal">İptal</button>
                <button id="btnSaveProd" class="btn btn-success">Kaydet</button>
            </div>
        </div></div></div>

<div class="modal fade" id="modalStok" tabindex="-1"><div class="modal-dialog"><div class="modal-content p-3">
            <h5>Stok İşlemi</h5>
            <select id="selStokProd" class="form-select mb-2"></select>
            <select id="selStokType" class="form-select mb-2">
                <option value="giris">Giriş</option>
                <option value="cikis">Çıkış</option>
            </select>
            <input id="inpStokMiktar" class="form-control mb-2" placeholder="Miktar">
            <input id="inpStokAciklama" class="form-control mb-2" placeholder="Açıklama">
            <div class="text-end">
                <button class="btn btn-secondary me-2" data-bs-dismiss="modal">İptal</button>
                <button id="btnSaveStok" class="btn btn-primary">Uygula</button>
            </div>
        </div></div></div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function(){
        // DataTables
        var prodTable = $('#tblProd').DataTable({
            columns:[
                {data:'id'},{data:'ad'},{data:'aciklama'},{data:'birim'},{data:'kalan'},
                {data:null,orderable:false,render:function(_,_,row){
                        return '<button class="btn btn-sm btn-primary btnEditProd me-1" data-id="'+row.id+'">Düzenle</button>'+
                            '<button class="btn btn-sm btn-danger btnDelProd" data-id="'+row.id+'">Sil</button>';
                    }}
            ]
        });
        var stokTable = $('#tblStok').DataTable({
            columns:[
                {data:'tarih'},{data:'islem_turu'},{data:'urun'},{data:'miktar'},{data:'stokAciklama'},
                {data:null,orderable:false,render:function(_,_,row){
                        return '<button class="btn btn-sm btn-danger btnDelStok" data-id="'+row.id+'">Sil</button>';
                    }}
            ]
        });

        // Sayfa geçiş
        function showPage(p){
            $('#navDashboard,#navUrun,#navStok').removeClass('active');
            $('#pageDashboard,#pageUrun,#pageStok').hide();
            if(p==='dashboard'){
                $('#navDashboard').addClass('active');
                $('#pageDashboard').show();
                loadStats();
            } else if(p==='urun'){
                $('#navUrun').addClass('active');
                $('#pageUrun').show();
                loadProducts();
            } else {
                $('#navStok').addClass('active');
                $('#pageStok').show();
                loadStock();
            }
        }
        $('#navDashboard').click(()=>showPage('dashboard'));
        $('#navUrun').click(()=>showPage('urun'));
        $('#navStok').click(()=>showPage('stok'));

        // Dashboard verileri
        var chart;
        function loadStats(){
            $.post('',{action:'stats'},res=>{
                if(res.status==='success'){
                    $('#statTotalProd').text(res.totalProd);
                    $('#statTotalStock').text(res.totalStock);
                    var labels = res.topProducts.map(o=>o.ad);
                    var data = res.topProducts.map(o=>o.kalan);
                    if(chart) chart.destroy();
                    chart = new Chart($('#chartTopProducts'),{
                        type:'bar',
                        data:{labels:labels,datasets:[{label:'Kalan Stok',data:data}]},
                        options:{responsive:true,scales:{y:{beginAtZero:true}}}
                    });
                }
            });
        }

        // Ürün işlemleri
        function loadProducts(q=''){ $.post('',{action:'prod_list',q:q},res=>{ if(res.status==='success') prodTable.clear().rows.add(res.data).draw(); }); }
        let prodTimer;
        $('#searchProd').on('input',()=>{ clearTimeout(prodTimer); prodTimer=setTimeout(()=>loadProducts($('#searchProd').val()),300); });
        $('#btnOpenProdModal').click(()=>{
            $('#inpProdId,#inpProdAd,#inpProdAciklama').val(''); $('#selProdBirim').val('');
            $('#modalProd').modal('show');
        });
        $(document).on('click','.btnEditProd',function(){
            let row=prodTable.row($(this).closest('tr')).data();
            $('#inpProdId').val(row.id); $('#inpProdAd').val(row.ad);
            $('#inpProdAciklama').val(row.aciklama); $('#selProdBirim').val(row.birim);
            $('#modalProd').modal('show');
        });
        $('#btnSaveProd').click(()=>{
            let id=$('#inpProdId').val(), act=id?'prod_edit':'prod_add';
            $.post('',{
                action:act,id:id,
                ad:$('#inpProdAd').val(),
                aciklama:$('#inpProdAciklama').val(),
                birim:$('#selProdBirim').val()
            },res=>{
                if(res.status==='success'){ $('#modalProd').modal('hide'); loadProducts(); }
                else alert(res.message);
            });
        });
        $(document).on('click','.btnDelProd',function(){
            if(!confirm('Silinsin mi?')) return;
            $.post('',{action:'prod_delete',id:$(this).data('id')},res=>{
                if(res.status==='success') loadProducts(); else alert(res.message);
            });
        });

        // Stok işlemleri
        function loadStock(){
            $.post('',{action:'stok_list',start:$('#stokStart').val(),end:$('#stokEnd').val()},res=>{
                if(res.status==='success') stokTable.clear().rows.add(res.data).draw();
            });
        }
        $('#btnFilterStok').click(loadStock);
        $('#btnOpenStokModal').click(()=>{
            $.post('',{action:'prod_options'},res=>{
                $('#selStokProd').html(res.options);
                $('#modalStok').modal('show');
            });
        });
        $('#btnSaveStok').click(()=>{
            $.post('',{
                action:'stok_add',
                urun_id:$('#selStokProd').val(),
                tip:$('#selStokType').val(),
                miktar:$('#inpStokMiktar').val(),
                aciklama:$('#inpStokAciklama').val()
            },res=>{
                if(res.status==='success'){ $('#modalStok').modal('hide'); loadStock(); }
                else alert(res.message);
            });
        });
        $(document).on('click','.btnDelStok',function(){
            if(!confirm('Silinsin mi?')) return;
            $.post('',{action:'stok_delete',id:$(this).data('id')},res=>{
                if(res.status==='success') loadStock(); else alert(res.message);
            });
        });

        // Başlangıç sayfası
        showPage('dashboard');
    });
</script>
</body>
</html>
