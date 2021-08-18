<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Carbon\Carbon;

use App\Uni\EduContent;
use \Illuminate\Http\Response;

class ContentsController extends Controller
{
    protected $newRoute;
    protected $storeRoute;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "contents.create";
        $this->storeRoute = "contents.store";
    }

    /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $title = 'Carga de Contenidos';

        $lContents = EduContent::select('id_content',
                                        'file_name',
                                        'file_path',
                                        'file_type',
                                        'file_size',
                                        'is_deleted')
                                ->get();

        return view('mgr.contents.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('sGetRoute', 'contents.preview')
                                        ->with('lContents', $lContents);
    }

    public function create(Request $request)
    {
        $title = "Carga de archivo";

        return view('mgr.contents.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute);
    }

    public function store(Request $request)
    {
        try {
            $isFile = $request->isFile;

            if ($isFile) {
                $file = $request->file('theFile');
    
                $fileName = $file->getClientOriginalName();
                $fileExt = $file->getClientOriginalExtension();
                $filePath = $file->getRealPath();
                $fileSize = $file->getSize();
                $fileMimeType = $file->getMimeType();

                $fileType = "";

                /**
                 * 'video', 'pdf', 'image', 'audio', 'text', 'file', 'link'
                 */
                switch ($fileExt) {
                    case 'mp4':
                    case 'wmv':
                    case 'mov':
                    case 'avi':
                        $fileType = 'video';
                        $destinationPath = 'contents/videos';
                        $url = Storage::put($destinationPath, $file);
                        break;

                    case 'jpg':
                    case 'jpeg':
                    case 'gif':
                    case 'png':
                        $fileType = 'image';
                        $destinationPath = 'contents/images';
                        $url = Storage::put($destinationPath, $file);
                        break;

                    case 'pdf':
                        $fileType = 'pdf';
                        $destinationPath = 'contents/pdfs';
                        $url = Storage::put($destinationPath, $file);
                        break;

                    case 'mp3':
                        $fileType = 'audio';
                        $destinationPath = 'contents/audios';
                        $url = Storage::put($destinationPath, $file);
                        break;

                    case 'txt':
                        $fileType = 'text';
                        $destinationPath = 'contents/texts';
                        $url = Storage::put($destinationPath, $file);
                        break;

                    case '':
                        $fileType = 'link';
                        $destinationPath = '';
                        $url = Storage::put($destinationPath, $file);
                        break;
                    
                    default:
                        $fileType = 'file';
                        $destinationPath = 'contents/files';
                        // $dt = Carbon::now();
                        // $name = $dt->format('Y_m_d_h_m_s') . '.' . $file->getClientOriginalExtension();
                        $url = Storage::putFileAs($destinationPath, new File($filePath), $fileName);
                        break;
                }

                $oContent = new EduContent();

                $oContent->file_name = $fileName;
                $oContent->file_extension = $fileExt;
                $oContent->file_sys_name = $url;
                $oContent->file_path = Storage::url($url);
                $oContent->file_type = $fileType;
                $oContent->file_size = $fileSize;
            }
            else {

                $oContent = new EduContent();
    
                $oContent->file_name = $request->theName;
                $oContent->file_extension = "";
                $oContent->file_sys_name = "";
                $oContent->file_path = $request->theFile;
                $oContent->file_type = "link";
                $oContent->file_size = 0;
            }

            $oContent->is_deleted = false;
            $oContent->created_by_id = \Auth::id();
            $oContent->updated_by_id = \Auth::id();

            $oContent->save();
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('contents.index');
    }

    public function getPreview(Request $request)
    {
        $oContent = EduContent::find($request->id_file);

        $url = asset($oContent->file_path);
        $path = str_replace("public", "", $url);
        $path = str_replace("storage", "storage/app", $path);

        if ($oContent->file_extension == 'txt') {
            return file_get_contents($path);
        }

        return $path;
        
        // return asset("contents/videos/".$oContent->file_name);
        // $url = Storage::url("contents/videos/".$oContent->file_name);
        // return asset($url);
        
        // $contents = asset("storage/".$oContent->file_sys_name);
        // return $contents;
    }
}
