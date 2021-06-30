<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use PDF;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\AuditAllResult;
use App\StandarisasiAuditIso;
use App\EmployeeSync;
use App\User;
use App\LogProcess;

class AuditController extends Controller
{
  

	public function __construct()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
			if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
			{
				die();
			}
		}      
		$this->middleware('auth');

		$this->location = ['Assembly','Accounting','Body Process','Exim','Material Process','Surface Treatment','Educational Instrument','Standardization','QA Process','Chemical Process Control','Human Resources','General Affairs','Workshop and Maintenance Molding','Production Engineering','Maintenance','Procurement','Production Control','Warehouse Material','Warehouse Finished Goods','Welding Process','Case Tanpo CL Body 3D Room'];

    $this->point_sup = [
      'Jalan - Lantai - Tempat Kerja - Tembok - Atap', 
      'Kontrol Lemari Dokumen, Jig, Penyimpanan, Alat Kebersihan', 
      'Meja Kerja - Meja Office', 
      'Material, WIP', 
      'Mesin & Tools',
      'Pencegahan Kebakaran - Pencegahan Bencana - Barang Berbahaya - Barang Beracun',
      'Tempat Istirahat, Meeting Room, Lobby, Di Dalam Ruangan, Kantin',
      'Kedisiplinan'
    ];

    $this->point_1 = [
      'Pada koridor umum, lebar koridor dipastikan lebih dari 80cm dan garis pembatas tidak ada yang rusak atau terkelupas', 
      'Pastikan selalu melakukan pengecekan di dalam ruangan apakah ada sarang laba-laba, yogore atau debu yang tersisa', 
      'Barang yang tidak diperlukan tidak ada di tiang dan sekitar tembok. Diberi label pembagian tempat, label nama yang ditempatkan, label PIC kontrol', 
      'Sebagai prinsipnya dilarang menempel informasi selain di papan informasi. Selain itu ditetapkan label PIC untuk papan informasi. Papan informasi di letakkan  lurus, sejajar , siku siku dan diberi 2 stopper atau lebih agar posisinya fix', 
      'Permukaan lantai tidak ada yang rusak, selalu dibersihkan dan tidak ada yang melebihi garis pembatas sampai ke koridor/jalan. Maintenance jika ada kerusakan cat koridor dll',
      '',
      '',
      'Untuk menempel informasi di papan informasi selain IK dll  menggunakan kertas "seminimum mungkin", lalu dimasukkan ke hardcase.  di tempel lurus, sejajar , siku siku dan diberi 2 stopper atau lebih agar posisinya fix '
    ];
    
    $this->point_2 = [
      'Tidak menaruh barang di bagian atas almari/rak', 
      'Dokumen yang disimpan dipilah(seiri) dan dirapikan(seiton), pastikan saat filling terpasang insert paper di punggung file.', 
      'Pintu lemari, pintu lemari jig, pintu lemari penyimpanan, pintu lemari alat kebersihan semua harus ditutup. Pintu yang rusak di repair. Dilakukan maintenance agar pintu bisa dibuka tutup dengan lancar', 
      'Pada lemari dokumen,lemari jig, lemari alat kebersihan dan lemari yang terkunci saja diperjelas dan ditetapkan PIC kontrolnya ,dilakukan pengontrolan agar tidak membawa  dan memindahkan kunci tanpa ijin ke tempat lain.', 
      'Memberi label PIC 5S lemari, lemari jig, lemari penyimpanan, lemari alat kebersihan ', 
      'Peletakkan lemari, lemari jig, lemari penyimpanan, lemari alat kebersihan di letakkan  lurus, sejajar dan siku-siku.', 
      'Harus menggantungkan sapu, pengepel, cikrak dll. Bila ember diletakkan di dalam lemari harus diberi batas dengan jelas dan papan nama.', 
      'Melakukan seiton dan semua diberi label untuk lemari buku, lemari jig dan lemari penyimpanan  agar barang yang diperlukan bisa langsung diambil tanpa perlu mencari-cari.'
    ];

    $this->point_3 = [
      'Melakukan SEIRI barang di atas meja kerja dan meja office,dijaga kebersihannya, kursi juga ditempatkan di tempat yang telah ditentukan', 
      'Dokumen yang digunakan tidak hanya disimpan tapi juga harus disusun secara jelas dengan menggunakan clip, tray, clear file agar bisa dibedakan dan agar bisa disimpan dengan lurus dan sejajar selama bekerja (termasuk ketika meninggalkan tempat). jangan menyimpan dokumen di atas meja', 
      'Saat pulang diatas meja kerja, hanya diletakkan benda benda yang sedang dikerjakan. Semua tools dikembalikan ke sugata oki. Sugata oki harus tepat tidak boleh lebih atau kurang saat dikembalikan', 
      'Diatas meja kerja sebisa mungkin hanya diletakkan dokumen yang dibutuhkan saja dan agar mudah diambil diberi sugata oki untuk tools. Prinsip pada saat bekerja adalah setiap setelah menggunakan tools langsung dikembalikan ke tempat semula.', 
      'Untuk semua meja kerja dan meja office diberi label PIC 5S yang telah ditentukan', 
      'Barang pribadi di letakkan di loker yang sudah ditetapkan ( tas, barang bawaan, pakaian), jangan diletakkan di sekitar meja kerja.', 
      'Dokumen yang disimpan meja kerja dan meja office disimpan dipilah(seiri) dan dirapikan(seiton), pastikan saat filling terpasang inset paper di punggung file.',
      'Meja kerja dan isi di laci di meja kerja adalah benda benda yang dibutuhkan untuk bekerja saja. Tools di meja kerja diberi sugata oki dan di laci hanya benda benda yang dibutuhkan untuk kerja.  dipilah(seiri) dan dirapikan(seiton) harus selalu dilakukan.'
     ];

    $this->point_4 = [
      'Perhatikan ketinggian saat menumpuk barang, ditumpuk sesuai dengan jangkauan tangan pekerja. Jangan ada yang miring atau keluar batas pada saat menumpuk.', 
      'Tempat peletakan returnable box dan pallet yang kosong di tetapkan PIC dan diberi label keduanya  ', 
      'Tempat menaruh barang bergerak/berpindah-pindah seperti daisha, dsb ditentukan dan diberi label. Untuk barang yang tidak ditentukan tempatnya, diberi label Temporary place/tempat menaruh sementara diperjelas, ditentukan  PIC  dan batas waktu/sampai kapan penempatannya', 
      'Penerapan langsung 3T dan pemberian label  (Tetap posisi・・menentukan tempat peletakan barang, Tetap jumlah・・menentukan jumlah yang diletakkan, Tetap barang・・menentukan barang yang diletakkan).', 
      'Di tempat penyimpanan material KD diberi visual control/ label jumlah material existing, MAX material, MIN material, kapan harus order dll. Selain itu harus mematuhi jumlah tersebut ', 
      'Tidak ada kerusakan pada palet dan returnable box yang dapat merusak produk, dan terjaga dengan baik dan bersih. sebagai prinsipnya selain yang tidak ada label nama perusahaan atau departemen yang bersangkutan tidak boleh dibawa.', 
      '',
      ''
     ];

    $this->point_5 = [
      'Ketika meninggalkan tempat duduk, pastikan kondisi display laptop/PC dan lampu dalam keadaan OFF', 
      'Keyboard dan monitor PC dirawat dengan baik agar tidak ada debu atau sidik jari yang tertinggal', 
      'Panjang dan jenis kabel tools OA sesuai & tidak berdebu. Disekitar stop kontak tidak ada debu.prinsip OA tools di letakkan  lurus, sejajar dan siku-siku.', 
      'Telepon diberi label nomor, kabel telepon tidak melilit, selalu terawat dan dalam kondisi bersih.  Letak telepon pada dasarnya diletakkan urus, sejajar dan siku-siku.', 
      'Seluruh mesin dan fasilitas, dibersihkan sampai ke ujung/sudut-sudutnya, kontrol oiling, daily maintenance. Dan ditampilkan record yang paling baru. (jangan ada sarang laba-laba atau debu yang terlihat)', 
      'Pipa dan kabel diatur agar panjangnya secukupnya tidak diletakkan di lantai. dipatenkan(dibuat fix) agar tidak membuat tersandung. Sebagai prinsipnya di letakkan  lurus, sejajar dan siku-siku.', 
      'Memberi tanda di sekitar mesin dan di mesin itu sendiri agar mudah dimengerti saat mesin sedang beroperasi seperti lampu atau kalimat pengumuman. untuk gas,air, air RO listrik dll diberi tanda arah aliran nya, lalu untuk benda yang berputar diberi label arah putaran dan sisa putaran.',
      'Seluruh mesin dan fasilitas diberi label PIC. Lalu, kunci di posisi yang benar (buka tutup, ON/OFF dll) dilepas dari mesin dan disimpan ditempat yang sudah ditentukan.'
     ];

    $this->point_6 = [
      'Lorong evakuasi terjaga dengan baik, tanda penunjuk dijaga agar tetap jelas.', 
      'Agar bisa segera diambil dari tempatnya (hydrant, APAR, tandu),usahakan tidak ada barang lain yang menghalangi. ', 
      'Lokasi alat pemadam dan tandu,dll diberi tanda agar bisa ditemukan walaupun dari kejauhan.', 
      'Benda beracun dan berbahaya disimpan di gudang yang ditetapkan dan diberi label nama dan quantity dan ada buku besar keluar masuknya barang, serta kunci gudangnya dikontrol.'
     ];

    $this->point_7 = [
      'Ditempatkan dispenser atau tempat minum , ditunjuk PIC nya untuk menjaga kebersihannya. Lalu, pastikan tidak ada bekas ciprtatan air di pantry, wastafel dan toilet agar terjaga keindahan nya. ', 
      'Tempat istirahat dan loker harus dijaga keindahan nya. Dilarang membawa barang barang yang tidak diperlukan. Isi loker harus dijaga jangan memasukkan barang yang tidak digunakan serta  dijaga kebersihan nya agar tetep indah . sudah menjadi tugas kita untuk menyimpan dan menjaga kebersihan barang barang perusahaan di loker', 
      'Penetapan penanggung jawab kontrol suhu AC (kontrol suhu artinya bukan untuk setting suhu, tetapi suhu aktual dalam ruangan). Meletakkan termometer di beberapa titik. Peletakan hydrometer dengan tepat.suhu dikontrol jangan sampai kurang dari 28 derajat celcius', 
      'Menentukan seluruh PIC jendela untuk dijiaga keindahan dan kebersihan nya sampai seperti tidak ada sidik jari yang menempel. Jangan menempel informasi di kaca. Lalu lemari atau fasilitas jangan menghalangi kaca ', 
      'Jam selalu menunjukkan waktu yang tepat', 
      'Menjaga kebersihan setelah memakai smoking room, buang sampah pada tempatnya, rapihkan kursi kembali setelah dipakai.', 
      '', 
      '', 
     ];

    $this->point_8 = [
      'Memakai name tag di dada sebelah kiri agar dapat dibaca dengan baik oleh orang lain.selalu memakai "seragam yang tepat" (untuk pria tutup kancing sampai atas . Untuk wanita resleting dari bawah sampai atas ), potong kuku, kaos dalam jangan keluar dari seragam, celana panjang, sepatu dll)', 
      'mengawali hari kerja dengan perasaan yang bersemangat. Harus mengucapkan salam di pagi, isang & sore hari di area kerja, serta senam harus dilakukan diluar ruangan dengan semangat dan tidak berbincang - bincang ', 
      'Tidak memasukkan tangan di saku pada saat berjalan, jangan berlarian di pabrik pada saat bekerja, pindah lokasi, istirahat siang. Pada saat menuruni tangga harus memegang pegangan tangga.', 
      'Pada tempat penyimpanan alat komunikasi seperti HP diberi label PIC, dan di  simpan. Space tempat penyimpanan yang kosong diberi label alasan "cuti:, "shift 2", "tidak digunakan"', 
      'Memastikan sekali lagi isi 5S dan 3 Tei (3 ketetapan), dilakukan training dan seluruhnya harus dihafal', 
      'Setiap hari pada saat pointing call hafal Filosofi Yamaha, Aturan K3 Yamaha, 6 Pasal Keselamatan Lalu Lintas Yamaha, dan 10 Komitmen Berkendara, dll', 
      'Pada saat bel pertama cepat segera kembali ke tempat kerja, bersamaan dengan bunyi bel langsung segera bekerja kembali',
      'Memahami dan mengaplikasikan untuk terus membersihkan ketika menyadari bahwa ada yang kotor di meja kerja, equipment, peralatan, kotak obat P3K, lemari, bola lampu, jendela, sampah yang terjatuh, dll. '
     ];

	}

  public function index()
  {
    $title = "YMPI Internal Patrol";
    $title_jp = "内部パトロール";

    return view('audit.index_patrol', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPI Patrol'); 
  }

  public function index_audit()
  {
    $title = "YMPI Internal Audit";
    $title_jp = "内部監査";

    return view('audit.index_audit', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPI Patrol'); 
  }

	public function index_patrol()
	{
		$title = "5S Patrol GM & Presdir";
		$title_jp = "社長、部長の5Sパトロール";

		$emp = EmployeeSync::where('employee_id', Auth::user()->username)
		->select('employee_id', 'name', 'position', 'department')->first();

		$auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
			where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

		return view('audit.patrol', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'employee' => $emp,
			'auditee' => $auditee,
			'location' => $this->location,
      'poin' => $this->point_sup,
      'point_1' => $this->point_1,
      'point_2' => $this->point_2,
      'point_3' => $this->point_3,
      'point_4' => $this->point_4,
      'point_5' => $this->point_5,
      'point_6' => $this->point_6,
      'point_7' => $this->point_7,
      'point_8' => $this->point_8
		))->with('page', 'Audit Patrol');
	}

  public function index_patrol_daily()
  {
    $title = "Patrol Daily Shift 1 & 2";
    $title_jp = "1・2直パトロール";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.patrol_daily', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Patrol Daily');
  }

  public function index_patrol_covid()
  {
    $title = "Patrol Covid";
    $title_jp = "コロナ対策パトロール";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.patrol_covid', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Patrol Covid');
  }

  public function fetch_patrol(Request $request){


    $data_all = db::select("
      SELECT
        kategori,
        sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
      FROM
        audit_all_results 
      WHERE jenis = 'Patrol'
      and point_judul != 'Positive Finding'
      GROUP BY
        kategori
      ORDER BY jumlah_belum ASC
    ");

    $data_type_all = db::select("
      SELECT
        point_judul,
        sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
      FROM
        audit_all_results 
      WHERE point_judul is not null
      and jenis = 'Patrol'
      and point_judul != 'Negative Finding'
      and point_judul != 'Positive Finding'
      GROUP BY
        point_judul
      ORDER BY point_judul ASC
    ");

    $response = array(
      'status' => true,
      'data_all' => $data_all,
      'data_type_all' => $data_type_all
    );

    return Response::json($response);
  }

  public function index_mis()
  {
    $title = "Audit MIS";
    $title_jp = "MIS監査";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.patrol_mis', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Audit Patrol MIS');
  }

  public function index_std()
  {
    $title = "EHS & 5S Monthly Patrol";
    $title_jp = "EHS・5S月次パトロール";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.patrol_std', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'EHS dan 5S Bulanan');
  }

  public function index_audit_stocktaking()
  {
    $title = "Audit Stocktaking";
    $title_jp = "棚卸監査";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%' or position like '%Leader%')");

    return view('audit.audit_stocktaking', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Audit Stocktaking');
  }

  public function index_audit_mis()
  {
    $title = "Audit MIS";
    $title_jp = "MIS監査";

    $emp = EmployeeSync::where('employee_id', Auth::user()->username)
    ->select('employee_id', 'name', 'position', 'department')->first();

    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

    return view('audit.audit_mis', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'employee' => $emp,
      'auditee' => $auditee,
      'location' => $this->location
    ))->with('page', 'Audit MIS');
  }


	public function post_audit(Request $request)
	{
		$audit = $request->get("audit");
		$datas = [];

		for ($i=0; $i < count($request->get('patrol_lokasi')); $i++) { 
			$patrol = new AuditAllResult;
			$patrol->tanggal = date('Y-m-d');
      $patrol->jenis = 'Patrol';
			$patrol->kategori = $request->get('category');
			$patrol->auditor_id = $request->get('auditor_id') ;
			$patrol->auditor_name = $request->get('auditor_name');
			$patrol->lokasi = $request->get('patrol_lokasi')[$i];
			$patrol->auditee_name = $request->get('patrol_pic')[$i];
			$patrol->point_judul = $request->get('patrol_detail')[$i];
			$patrol->note = $request->get('note')[$i];
			$patrol->created_by = Auth::id();
			$patrol->save();
		}

		$response = array(
			'status' => true,
		);
		return Response::json($response);
	}

	public function post_audit_file(Request $request)
	{
		try {
			$id_user = Auth::id();
			$tujuan_upload = 'files/patrol';

      $poin_sup = "";
      $detail_poin_sup = "";

      if ($request->input('poin_fix') == "" || $request->input('poin_fix') == null || $request->input('poin_fix') == "#0") {
        $poin_sup = null;
      }
      else{
        $poin_sup = $request->input('poin_fix');
      }

      if ($request->input('isi_poin_fix') == "" || $request->input('isi_poin_fix') == null || $request->input('isi_poin_fix') == "#1") {
        $detail_poin_sup = null;
      }
      else{
        $detail_poin_sup = $request->input('isi_poin_fix');
      }

      
			for ($i=0; $i < $request->input('jumlah'); $i++) { 

				$file = $request->file('file_datas_'.$i);
				$nama = $file->getClientOriginalName();

				$filename = pathinfo($nama, PATHINFO_FILENAME);
				$extension = pathinfo($nama, PATHINFO_EXTENSION);

				$filename = md5($filename.date('YmdHisa')).'.'.$extension;

				$file->move($tujuan_upload,$filename);

				$audit_all = AuditAllResult::create([
          'jenis' => 'Patrol',
					'tanggal' => date('Y-m-d'),
					'kategori' => $request->input('category'),
					'lokasi' => $request->input('location'),
					'auditor_id' => $request->input('auditor_id'),
					'auditor_name' => $request->input('auditor_name'),
					'auditee_name' => $request->input('patrol_pic_'.$i),
					'point_judul' => $request->input('patrol_detail_'.$i),
					'note' => $request->input('note_'.$i),
          'poin_sup' => $poin_sup,
          'detail_poin_sup' => $detail_poin_sup,
					'foto' => $filename,
					'created_by' => $id_user
				]);

        $id = $audit_all->id;

        if ($request->input('patrol_detail_'.$i) != "Positive Finding") {
          $mails = "select distinct email from users where name = '".$request->input('patrol_pic_'.$i)."'";
          $mailtoo = DB::select($mails);

          $isimail = "select * from audit_all_results where id = ".$id;

          $auditdata = db::select($isimail);

          if ($request->input('category') == "Patrol Daily" || $request->input('category') == "Patrol Covid") {
            $mailscc = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where section = 'Secretary Admin Section' and employee_id != 'PI9704001'";  
            $mailtoocc = DB::select($mailscc);

            Mail::to($mailtoo)->cc($mailtoocc)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
          } 
          else{
            Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
          }
        }

        

			}

			$response = array(
				'status' => true,
			);
			return Response::json($response);
		} 

    catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage()
			);
			return Response::json($response);
		}
	}

  public function post_audit_stocktaking(Request $request)
  {
    try {
      $id_user = Auth::id();
      $tujuan_upload = 'files/patrol';

      for ($i=0; $i < $request->input('jumlah'); $i++) { 

        $file = $request->file('file_datas_'.$i);
        $nama = $file->getClientOriginalName();

        $filename = pathinfo($nama, PATHINFO_FILENAME);
        $extension = pathinfo($nama, PATHINFO_EXTENSION);

        $filename = md5($filename.date('YmdHisa')).'.'.$extension;

        $file->move($tujuan_upload,$filename);

        $audit_all = AuditAllResult::create([
          'jenis' => 'Audit',
          'tanggal' => date('Y-m-d'),
          'kategori' => $request->input('category'),
          'lokasi' => $request->input('location'),
          'auditor_id' => $request->input('auditor_id'),
          'auditor_name' => $request->input('auditor_name'),
          'auditee_name' => $request->input('patrol_pic_'.$i),
          'point_judul' => $request->input('patrol_detail_'.$i),
          'note' => $request->input('note_'.$i),
          'foto' => $filename,
          'created_by' => $id_user
        ]);

        $id = $audit_all->id;

        $mails = "select distinct email from users where name = '".$request->input('patrol_pic_'.$i)."'";
        $mailtoo = DB::select($mails);

        $isimail = "select * from audit_all_results where id = ".$id;

        $auditdata = db::select($isimail);

        Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
      }

      $response = array(
        'status' => true,
      );
      return Response::json($response);
    } 

    catch (\Exception $e) {
      $response = array(
        'status' => false,
        'message' => $e->getMessage()
      );
      return Response::json($response);
    }
  }


	public function fetch_audit(Request $request)
	{
		try {

			$kategori = $request->get("category");

			$query = 'SELECT * FROM standarisasi_audit_checklists where point_question is not null and deleted_at is null and kategori = "'.$kategori.'" order by id asc';
			$detail = db::select($query);

			$response = array(
				'status' => true,
				'lists' => $detail
			);

			return Response::json($response);

		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message'=> $e->getMessage()
			);

			return Response::json($response); 
		}
	}


	public function indexMonitoring(){

   return view('audit.patrol_monitoring',  
     array(
       'title' => 'Patrol Monitoring', 
       'title_jp' => 'パトロール監視',
     )
   )->with('page', 'Audit Patrol');
 }

 public function fetchMonitoring(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $first = date("Y-m-d", strtotime('-30 days'));

  $last = AuditAllResult::whereNull('status_ditangani')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as tanggal'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
      }
    }
  }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $data = db::select("SELECT
    date_format(tanggal, '%a, %d %b %Y') AS tanggal,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_belum_gm,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_sudah_gm,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_belum_presdir,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_sudah_presdir 
    FROM
    audit_all_results 
    WHERE
    tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
    and kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM')
    GROUP BY
    tanggal");

  $data_kategori = db::select("
  SELECT
    kategori,
    sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani IS NOT NULL THEN 1 ELSE 0 END ) AS jumlah_sudah
  FROM
    audit_all_results 
  WHERE
    kategori IN ( 'S-Up And EHS Patrol Presdir', '5S Patrol GM' ) 
  GROUP BY
    kategori");

  $data_bulan = db::select("
    SELECT
    MONTHNAME(tanggal) as bulan,
    year(tanggal) as tahun,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_belum_gm,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_sudah_gm,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_belum_presdir,
    sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_sudah_presdir 
    FROM
    audit_all_results 
    WHERE
    kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM')
    GROUP BY
    tahun,monthname(tanggal)
    order by tahun, month(tanggal) ASC"
  );

  $year = date('Y');

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_kategori' => $data_kategori,
    'data_bulan' => $data_bulan,
    'year' => $year
  );

  return Response::json($response);
}

public function detailMonitoring(Request $request){

    $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      if(strlen($request->get('datefrom')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
      }

      if(strlen($request->get('dateto')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      }

      $status = $request->get('status');

      if ($status != null) {

      if ($status == "Temuan GM Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Open"){
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "S-Up And EHS Patrol Presdir"';
      }
      else if ($status == "Temuan GM Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "S-Up And EHS Patrol Presdir"';
      }


    } else{
      $stat = '';
    }

    $datefrom = $request->get('datefrom');
    $dateto = $request->get('dateto');

    if ($datefrom != null && $dateto != null) {
      $df = 'and audit_all_results.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
    }else{
      $df = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and tanggal = '".$tgl."' ".$stat." and point_judul != 'Positive Finding'";

    $detail = db::select($query);

    return DataTables::of($detail)

    ->editColumn('auditor_name', function($detail){
      $kategori = '';

      if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        $kategori = "Presdir";
      }else if ($detail->kategori == "5S Patrol GM"){
        $kategori = "GM";
      }else{
        $kategori = $detail->kategori;
      }

      $tgl = date('d-M-Y', strtotime($detail->tanggal));

     return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
    })


    ->editColumn('foto', function($detail){
      return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('auditee_name', function($detail){
      return $detail->point_judul.'<br>'.$detail->auditee_name;
    })

    ->editColumn('penanganan', function($detail){

      $bukti = "";

      if ($detail->bukti_penanganan != null) {
        $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
      }else{
        $bukti = "";
      }

      return $detail->penanganan.''.$bukti;
    })

    ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}


public function detailMonitoringCategory(Request $request){

    $kategori = $request->get('kategori');
    $status = $request->get('status');

    if ($status != null) {

      if ($status == "Temuan Belum Ditangani") {
        $stat = 'and audit_all_results.status_ditangani is null';
      }
      else if ($status == "Temuan Sudah Ditangani"){
        $stat = 'and audit_all_results.status_ditangani is not null';
      }

    } else{
      $stat = '';
    }

    if ($kategori == "EHS 5S Monthly Patrol") {
      $kategori = "EHS & 5S Patrol";
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and kategori = '".$kategori."' ".$stat." and point_judul != 'Positive Finding'";

    $detail = db::select($query);

    return DataTables::of($detail)

    ->editColumn('auditor_name', function($detail){
      $kategori = '';

      if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        $kategori = "Presdir";
      }else if ($detail->kategori == "5S Patrol GM"){
        $kategori = "GM";
      }else{
        $kategori = $detail->kategori;
      }

      $tgl = date('d-M-Y', strtotime($detail->tanggal));

     return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
    })


    ->editColumn('foto', function($detail){
      return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('auditee_name', function($detail){
      return $detail->point_judul.'<br>'.$detail->auditee_name;
    })

    ->editColumn('penanganan', function($detail){

      $bukti = "";

      if ($detail->bukti_penanganan != null) {
        $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
      }else{
        $bukti = "";
      }

      return $detail->penanganan.''.$bukti;
    })

    ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}

public function detailMonitoringBulan(Request $request){

    $bulan = $request->get('bulan');
    $status = $request->get('status');

    if ($status != null) {

      if ($status == "Temuan GM Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Open"){
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "S-Up And EHS Patrol Presdir"';
      }
      else if ($status == "Temuan GM Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "5S Patrol GM"';
      }
      else if ($status == "Temuan Presdir Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "S-Up And EHS Patrol Presdir"';
      }

    } else{
      $stat = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and monthname(tanggal) = '".$bulan."' ".$stat." and point_judul != 'Positive Finding'";

    $detail = db::select($query);

    return DataTables::of($detail)

    ->editColumn('auditor_name', function($detail){
      $kategori = '';

      if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        $kategori = "Presdir";
      }else if ($detail->kategori == "5S Patrol GM"){
        $kategori = "GM";
      }else{
        $kategori = $detail->kategori;
      }

      $tgl = date('d-M-Y', strtotime($detail->tanggal));

     return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
    })


    ->editColumn('foto', function($detail){
      return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('auditee_name', function($detail){
      return $detail->point_judul.'<br>'.$detail->auditee_name;
    })

    ->editColumn('penanganan', function($detail){

      $bukti = "";

      if ($detail->bukti_penanganan != null) {
        $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
      }else{
        $bukti = "";
      }

      return $detail->penanganan.''.$bukti;
    })

    ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}

public function detailMonitoringType(Request $request){

    $type = $request->get('type');
    $status = $request->get('status');

    if ($status != null) {

     if ($status == "Temuan Belum Ditangani") {
        $stat = 'and audit_all_results.status_ditangani is null';
      }
      else if ($status == "Temuan Sudah Ditangani"){
        $stat = 'and audit_all_results.status_ditangani is not null';
      }

    } else{
      $stat = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and point_judul = '".$type."' ".$stat." and point_judul != 'Positive Finding'";

    $detail = db::select($query);

    return DataTables::of($detail)

   ->editColumn('auditor_name', function($detail){
      $kategori = '';

      if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        $kategori = "Presdir";
      }else if ($detail->kategori == "5S Patrol GM"){
        $kategori = "GM";
      }else{
        $kategori = $detail->kategori;
      }

      $tgl = date('d-M-Y', strtotime($detail->tanggal));

     return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
    })


    ->editColumn('foto', function($detail){
      return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('auditee_name', function($detail){
      return $detail->point_judul.'<br>'.$detail->auditee_name;
    })

    ->editColumn('penanganan', function($detail){

      $bukti = "";

      if ($detail->bukti_penanganan != null) {
        $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
      }else{
        $bukti = "";
      }

      return $detail->penanganan.''.$bukti;
    })

    ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
}

public function fetchtable_audit(Request $request)
{

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $last = AuditAllResult::whereNull('status_ditangani')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as audit_date'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }else{
    if($last){
      $tanggal = date_create($last->audit_date);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->audit_date));
      }
    }
  }


  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $status = $request->get('status');

  if ($status != null) {
    $cat = json_encode($status);
    $kat = str_replace(array("[","]"),array("(",")"),$cat);

    $kate = 'and audit_all_results.status_ditangani in'.$kat;
  }else{
    $kate = 'and audit_all_results.status_ditangani is null';
  }


  $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM') and tanggal between '".$datefrom."' and '".$dateto."' ".$kate." ");

  $response = array(
    'status' => true,
    'datas' => $data
  );

  return Response::json($response); 
}


public function detailPenanganan(Request $request){
  $audit = db::select("SELECT
   * from audit_all_results where id = ". $request->get('id'));

    $response = array(
     'status' => true,
     'audit' => $audit,
   );
    return Response::json($response);
  }

  public function editAudit(Request $request)
  {
    try{
      $audit = AuditAllResult::find($request->get("id"));
      $audit->note = $request->get('note');
      $audit->lokasi = $request->get('lokasi');
      $audit->save();

      $response = array(
        'status' => true,
        'datas' => "Berhasil",
      );
      return Response::json($response);
    }
    catch (QueryException $e){
      $error_code = $e->errorInfo[1];
      if($error_code == 1062){
       $response = array(
        'status' => false,
        'datas' => "Audit Already Exist",
      );
       return Response::json($response);
     }
     else{
       $response = array(
        'status' => false,
        'datas' => $e->getMessage(),
      );
       return Response::json($response);
     }
   }
 }

 public function postPenanganan(Request $request)
 {
  try{
    $audit = AuditAllResult::find($request->get("id"));
    $audit->penanganan = $request->get('penanganan');
    $audit->tanggal_penanganan = date('Y-m-d');
    $audit->status_ditangani = 'close';
    $audit->save();

    $response = array(
      'status' => true,
      'datas' => "Berhasil",
    );
    return Response::json($response);
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
     $response = array(
      'status' => false,
      'datas' => "Audit Already Exist",
    );
     return Response::json($response);
   }
   else{
     $response = array(
      'status' => false,
      'datas' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }
}

  public function postPenangananNew(Request $request)
   {
    try{
      $id_user = Auth::id();
      $tujuan_upload = 'files/patrol';

      $file = $request->file('bukti_penanganan');
      $nama = $file->getClientOriginalName();
      $filename = pathinfo($nama, PATHINFO_FILENAME);
      $extension = pathinfo($nama, PATHINFO_EXTENSION);
      $filename = md5($filename.date('YmdHisa')).'.'.$extension;
      $file->move($tujuan_upload,$filename);

      $audit = AuditAllResult::find($request->input("id"));
      $audit->penanganan = $request->input('penanganan');
      $audit->bukti_penanganan = $filename;
      $audit->tanggal_penanganan = date('Y-m-d');
      $audit->status_ditangani = 'close';
      $audit->save();

      $response = array(
        'status' => true,
      );
      return Response::json($response);
    }
    catch (QueryException $e){
      $error_code = $e->errorInfo[1];
      if($error_code == 1062){
       $response = array(
        'status' => false,
        'datas' => "Audit Already Exist",
      );
       return Response::json($response);
     }
     else{
       $response = array(
        'status' => false,
        'datas' => $e->getMessage(),
      );
       return Response::json($response);
     }
   }
  }

  public function exportPatrol(Request $request){
      $time = date('d-m-Y H;i;s');

      $tanggal = "";
      $status = "";

      if (strlen($request->get('date')) > 0)
      {
          $date = date('Y-m-d', strtotime($request->get('date')));
          $tanggal = "and tanggal = '" . $date . "'";
      }

      if (strlen($request->get('status')) > 0)
      {
          if($request->get('status') == 'Temuan GM Close') {
            $status = "and kategori = '5S Patrol GM' and status_ditangani is not null";
          }
          else if ($request->get('status') == 'Temuan GM Open') {
            $status = "and kategori = '5S Patrol GM' and status_ditangani is null";
          }
          else if ($request->get('status') == 'Temuan Presdir Close') {
            $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani is not null";
          }
          else if ($request->get('status') == 'Temuan Presdir Open') {
            $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani is null";
          }
      }

      $detail = db::select(
          "SELECT DISTINCT audit_all_results.* from audit_all_results WHERE audit_all_results.deleted_at IS NULL ".$tanggal." ".$status." order by id ASC");

      $data = array(
          'detail' => $detail
      );

      ob_clean();

      Excel::create('Audit List '.$time, function($excel) use ($data){
          $excel->sheet('Data', function($sheet) use ($data) {
            return $sheet->loadView('audit.audit_excel', $data);
        });
      })->export('xlsx');
    }

    public function exportPatrolAll(Request $request){
      $time = date('d-m-Y H;i;s');

      $tanggal = "";
      $kategori = "";

      if (strlen($request->get('date')) > 0)
      {
          $date = date('Y-m-d', strtotime($request->get('date')));
          $tanggal = "and tanggal = '" . $date . "'";
      }

      if (strlen($request->get('category_export')) > 0)
      {

          if ($request->get('category_export') == "monthly_patrol") {
            $category = "EHS & 5S Patrol";
          }
          else if ($request->get('category_export') == "daily_patrol") {
            $category = "Patrol Daily";
          }
          else if ($request->get('category_export') == "covid_patrol") {
            $category = "Patrol Covid";
          }
          else if ($request->get('category_export') == "stocktaking") {
            $category = "Audit Stocktaking";
          }
          else if ($request->get('category_export') == "mis") {
            $category = "Audit MIS";
          }

          $kategori = "and kategori = '".$category."'";
      }

      $detail = db::select(
          "SELECT DISTINCT audit_all_results.* from audit_all_results WHERE audit_all_results.deleted_at IS NULL ".$tanggal." ".$kategori." order by id ASC");

      $data = array(
          'detail' => $detail
      );

      ob_clean();

      Excel::create('Report '.$category.' '.$request->get('date'), function($excel) use ($data){
          $excel->sheet('Data', function($sheet) use ($data) {
            return $sheet->loadView('audit.audit_excel', $data);
        });

        $lastrow = $excel->getActiveSheet()->getHighestRow();    
        $excel->getActiveSheet()->getStyle('A1:F'.$lastrow)->getAlignment()->setWrapText(true); 
          // $excel->getActiveSheet()->getColumnDimension('A:F')->setAutoSize(false);

      })->export('xlsx');
    }




    // Audit & Patrol Monitoring All

    public function indexMonitoringAll($id){

      return view('audit.patrol_monitoring_all',  
         array(
           'title' => 'Audit & Patrol Monitoring', 
           'title_jp' => '監査・パトロールの表示',
           'category' => $id
         )
       )->with('page', 'Audit Patrol Monitoring');
     }

    public function fetchMonitoringAll(Request $request){

      $first = date("Y-m-d", strtotime('-30 days'));

      $check = AuditAllResult::where('status_ditangani', '=', 'close')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      if($first > date("Y-m-d", strtotime($check->tanggal))){
        $first = date("Y-m-d", strtotime($check->tanggal));
      }

      if ($request->get('category') == "monthly_patrol") {
        $category = "EHS & 5S Patrol";
      }
      else if ($request->get('category') == "daily_patrol") {
        $category = "Patrol Daily";
      }
      else if ($request->get('category') == "covid_patrol") {
        $category = "Patrol Covid";
      }
      else if ($request->get('category') == "stocktaking") {
        $category = "Audit Stocktaking";
      }
      else if ($request->get('category') == "mis") {
        $category = "Audit MIS";
      }

      $data = db::select("SELECT
        date_format(tanggal, '%a, %d %b %Y') AS tanggal,
        sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        audit_all_results 
        WHERE
        tanggal >= '".$first."'
        and kategori in ('".$category."')
        and point_judul != 'Positive Finding'
        GROUP BY
        tanggal");

      $data_bulan = db::select("
        SELECT
        MONTHNAME(tanggal) as bulan,
        year(tanggal) as tahun,
        sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        audit_all_results 
        WHERE
        kategori in ('".$category."')
        and point_judul != 'Positive Finding'
        GROUP BY
        tahun,monthname(tanggal)
        order by tahun, month(tanggal) ASC"
      );

      $response = array(
        'status' => true,
        'datas' => $data,
        'data_bulan' => $data_bulan,
        'category' => $category
      );

      return Response::json($response);
    }

    public function fetchTableAuditAll(Request $request)
    {
      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $last = AuditAllResult::whereNull('status_ditangani')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      $status = $request->get('status');

      if ($status != null) {
        $cat = json_encode($status);
        $kat = str_replace(array("[","]"),array("(",")"),$cat);

        $kate = 'and audit_all_results.status_ditangani in'.$kat;
      }else{
        $kate = 'and audit_all_results.status_ditangani is null';
      }


      if ($request->get('category') == "monthly_patrol") {
        $category = "EHS & 5S Patrol";
      }
      else if ($request->get('category') == "daily_patrol") {
        $category = "Patrol Daily";
      }
      else if ($request->get('category') == "covid_patrol") {
        $category = "Patrol Covid";
      }
      else if ($request->get('category') == "stocktaking") {
        $category = "Audit Stocktaking";
      }
      else if ($request->get('category') == "mis") {
        $category = "Audit MIS";
      }


      $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and kategori in ('".$category."') ".$kate." and point_judul != 'Positive Finding' ");

      $response = array(
        'status' => true,
        'datas' => $data
      );

      return Response::json($response); 
    }

    public function detailMonitoringAll(Request $request){

      $tgl = date('Y-m-d', strtotime($request->get("tgl")));

      $status = $request->get('status');

      if ($status != null) {

      if ($status == "Temuan Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
      }
      else if ($status == "Temuan Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
      }

    } else{
      $stat = '';
    }

    $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and tanggal = '".$tgl."' ".$stat."";

    $detail = db::select($query);

    return DataTables::of($detail)

    ->editColumn('auditor_name', function($detail){
      $kategori = '';

      if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        $kategori = "Presdir";
      }else if ($detail->kategori == "5S Patrol GM"){
        $kategori = "GM";
      }else{
        $kategori = $detail->kategori;
      }

      $tgl = date('d-M-Y', strtotime($detail->tanggal));

     return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
    })


    ->editColumn('foto', function($detail){
      return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('auditee_name', function($detail){
      return $detail->point_judul.'<br>'.$detail->auditee_name;
    })

    ->editColumn('penanganan', function($detail){

      $bukti = "";

      if ($detail->bukti_penanganan != null) {
        $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
      }else{
        $bukti = "";
      }

      return $detail->penanganan.''.$bukti;
    })

    ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
  }

  public function detailMonitoringBulanAll(Request $request){

    $bulan = $request->get('bulan');
    $status = $request->get('status');

    if ($status != null) {

      if ($status == "Temuan Open") {
        $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'" and point_judul != "Positive Finding" ';
      }
      else if ($status == "Temuan Close") {
        $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
      }

    } else{
      $stat = '';
    }

      $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and monthname(tanggal) = '".$bulan."' ".$stat."";

      $detail = db::select($query);

      return DataTables::of($detail)

      ->editColumn('auditor_name', function($detail){
      $kategori = '';

      if($detail->kategori == "S-Up And EHS Patrol Presdir"){
        $kategori = "Presdir";
      }else if ($detail->kategori == "5S Patrol GM"){
        $kategori = "GM";
      }else{
        $kategori = $detail->kategori;
      }

      $tgl = date('d-M-Y', strtotime($detail->tanggal));

     return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
    })


    ->editColumn('foto', function($detail){
      return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
    })

    ->editColumn('auditee_name', function($detail){
      return $detail->point_judul.'<br>'.$detail->auditee_name;
    })

    ->editColumn('penanganan', function($detail){

      $bukti = "";

      if ($detail->bukti_penanganan != null) {
        $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
      }else{
        $bukti = "";
      }

      return $detail->penanganan.''.$bukti;
    })

    ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
    ->make(true);
  }

    // Audit & Patrol By Team Monthly Patrol

    public function indexPatrolResume($id){

      return view('audit.patrol_monthly_team',  
         array(
           'title' => 'Monthly Patrol Resume', 
           'title_jp' => '月次パトロールめとめ',
           'category' => $id
         )
       )->with('page', 'Monthly Patrol Resume');
     }

    public function fetchPatrolResume(Request $request){

      $first = date("Y-m-d", strtotime('-30 days'));

      $check = AuditAllResult::where('status_ditangani', '=', 'close')
      ->orderBy('tanggal', 'asc')
      ->select(db::raw('date(tanggal) as audit_date'))
      ->first();

      if($first > date("Y-m-d", strtotime($check->tanggal))){
        $first = date("Y-m-d", strtotime($check->tanggal));
      }

      if ($request->get('month') != "") {
        $month = "and DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
      }else{
        $month = "";
      }

      if ($request->get('category') == "monthly_patrol") {
        $category = "EHS & 5S Patrol";
      }
      else if ($request->get('category') == "daily_patrol") {
        $category = "Patrol Daily";
      }
      else if ($request->get('category') == "covid_patrol") {
        $category = "Patrol Covid";
      }
      else if ($request->get('category') == "stocktaking") {
        $category = "Audit Stocktaking";
      }
      else if ($request->get('category') == "mis") {
        $category = "Audit MIS";
      }

      $data_bulan = db::select("
        SELECT
        auditor_name,
        sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        audit_all_results 
        WHERE
        kategori in ('".$category."')
        and point_judul != 'Positive Finding'
        ".$month."
        GROUP BY
        auditor_name ASC
        "
      );

      $data_lokasi = db::select("
        SELECT
        lokasi,
        sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani IS NOT NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        audit_all_results 
        WHERE
        kategori in ('".$category."')
        and point_judul != 'Positive Finding'
        ".$month."
        GROUP BY
        lokasi ASC
        "
      );

      $response = array(
        'status' => true,
        'data_bulan' => $data_bulan,
        'data_lokasi' => $data_lokasi,
        'category' => $category
      );

      return Response::json($response);
    }

    public function detailPatrolResume(Request $request){

      $auditor = $request->get('auditor');
      $status = $request->get('status');

      if ($request->get('month') != "") {
        $month = "and DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
      }else{
        $month = "";
      }

      if ($status != null) {

        if ($status == "Temuan Open") {
          $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
        }
        else if ($status == "Temuan Close") {
          $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
        }

      } else{
        $stat = '';
      }

        $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and auditor_name = '".$auditor."' ".$stat." ".$month." and point_judul != 'Positive Finding'";

        $detail = db::select($query);

        return DataTables::of($detail)

        ->editColumn('auditor_name', function($detail){
          $kategori = '';

          if($detail->kategori == "S-Up And EHS Patrol Presdir"){
            $kategori = "Presdir";
          }else if ($detail->kategori == "5S Patrol GM"){
            $kategori = "GM";
          }else{
            $kategori = $detail->kategori;
          }

          $tgl = date('d-M-Y', strtotime($detail->tanggal));

         return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
        })


        ->editColumn('foto', function($detail){
          return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
        })

        ->editColumn('auditee_name', function($detail){
          return $detail->point_judul.'<br>'.$detail->auditee_name;
        })

        ->editColumn('penanganan', function($detail){

          $bukti = "";

          if ($detail->bukti_penanganan != null) {
            $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
          }else{
            $bukti = "";
          }

          return $detail->penanganan.''.$bukti;
        })

        ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
        ->make(true);
    }

    public function detailLokasiPatrolResume(Request $request){

      $lokasi = $request->get('lokasi');
      $status = $request->get('status');

      if ($request->get('month') != "") {
        $month = "and DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
      }else{
        $month = "";
      }

      if ($status != null) {

        if ($status == "Temuan Open") {
          $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
        }
        else if ($status == "Temuan Close") {
          $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
        }

      } else{
        $stat = '';
      }

        $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and lokasi = '".$lokasi."' ".$stat." ".$month." and point_judul != 'Positive Finding'";

        $detail = db::select($query);

        return DataTables::of($detail)

        ->editColumn('auditor_name', function($detail){
          $kategori = '';

          if($detail->kategori == "S-Up And EHS Patrol Presdir"){
            $kategori = "Presdir";
          }else if ($detail->kategori == "5S Patrol GM"){
            $kategori = "GM";
          }else{
            $kategori = $detail->kategori;
          }

          $tgl = date('d-M-Y', strtotime($detail->tanggal));

         return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
        })


        ->editColumn('foto', function($detail){
          return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
        })

        ->editColumn('auditee_name', function($detail){
          return $detail->point_judul.'<br>'.$detail->auditee_name;
        })

        ->editColumn('penanganan', function($detail){

          $bukti = "";

          if ($detail->bukti_penanganan != null) {
            $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
          }else{
            $bukti = "";
          }

          return $detail->penanganan.''.$bukti;
        })

        ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
        ->make(true);
    }

    public function ExportMonthlyPatrolResume(){
      $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and audit_all_results.status_ditangani = 'close' and kategori = 'EHS & 5S Patrol'";

      $detail = db::select($query);

      return view('audit.patrol_monthly_team_export',  
         array(
           'title' => 'Monthly Patrol By Location List', 
           'title_jp' => '場所別の月次パトロール',
           'data' => $detail
         )
       )->with('page', 'Monthly Patrol By Location List');

    }


  public function index_packing_documentation()
  { 
    $title = "Packing Documentation";
    $title_jp = "梱包作業の書類化";

    return view('documentation.index_packing_documentation', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'Packing Documentation'); 
  }


  public function packing_documentation($loc)
  { 
    if ($loc == 'fl') {
      $loc = 'Flute';

      $data = LogProcess::select('*')
      ->where('origin_group_code','=','041')
      ->where('process_code','=','6')
      ->get();
    } 
    else if ($loc == 'cl') {
      $loc = 'Clarinet';

      $data = "";
    } 
    else if ($loc == 'sx') {
      $data = LogProcess::select('*')
      ->where('origin_group_code','=','043')
      ->where('process_code','=','4')
      ->get();
    }
    else{
      $loc = $loc;
    }

    $title = "Packing Documentation ".$loc;
    $title_jp = "梱包作業の書類化";

    $user = User::where('username', Auth::user()->username)
    ->select('*')
    ->first();

    return view('documentation.packing_documentation', array(
      'title' => $title,
      'title_jp' => $title_jp,
      'user' => $user,
      'data' => $data
    ))->with('page', 'Packing Documentation'); 
  }

  public function documentation_data(Request $request){

      try{

        $sn = LogProcess::select('log_processes.*')
        ->where('serial_number','=',$request->get('tag'))
        ->where('origin_group_code','=','041')
        ->where('process_code','=','6')
        ->whereNull('log_processes.deleted_at')
        ->first();

        if (count($sn) > 0) {
            $response = array(
                'status' => true,
                'message' => 'Base Data Ditemukan',
                'sn' => $sn
            );
            return Response::json($response);
        }
        else{
            $response = array(
                'status' => false,
                'message' => 'Data Tidak Ditemukan',
            );
            return Response::json($response);
        }

      }catch(\Exception $e){
          $response = array(
              'status' => false,
              'message' => $e->getMessage(),
          );
          return Response::json($response);
      }
  }

  public function documentation_post(Request $request)
    {
        try{
            $stock = new MpKanagataOrder([
              'tanggal' => date('Y-m-d H:i:s'),
              'employee_id' => $request->get('employee_id'),
              'employee_name' => $request->get('employee_name'),
              'serial_number' => $request->get('serial_number'),
              'model' => $model->get('model'),
              'created_by' => Auth::user()->username
            ]);

            $stock->save();
            $response = array(
              'status' => true,
              'message' => 'Data Dokumentasi Serial Number Berhasil Dimasukkan'
            );
            return Response::json($response);

        }
        catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }
}
