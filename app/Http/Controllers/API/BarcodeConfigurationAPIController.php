<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBarcodeConfigurationAPIRequest;
use App\Http\Requests\API\UpdateBarcodeConfigurationAPIRequest;
use App\Models\BarcodeConfiguration;
use App\Repositories\BarcodeConfigurationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;
use App\Scopes\ActiveScope;
use App\Models\Company;
use DNS1D;
use App\Models\FixedAssetMaster;
use DNS2D;
use Illuminate\Support\Facades\Storage;
use TCPDF;
/**
 * Class BarcodeConfigurationController
 * @package App\Http\Controllers\API
 */

class BarcodeConfigurationAPIController extends AppBaseController
{
    /** @var  BarcodeConfigurationRepository */
    private $barcodeConfigurationRepository;

    public function __construct(BarcodeConfigurationRepository $barcodeConfigurationRepo)
    {
        $this->barcodeConfigurationRepository = $barcodeConfigurationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/barcodeConfigurations",
     *      summary="Get a listing of the BarcodeConfigurations.",
     *      tags={"BarcodeConfiguration"},
     *      description="Get all BarcodeConfigurations",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/BarcodeConfiguration")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->barcodeConfigurationRepository->pushCriteria(new RequestCriteria($request));
        $this->barcodeConfigurationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $barcodeConfigurations = $this->barcodeConfigurationRepository->all();

        return $this->sendResponse($barcodeConfigurations->toArray(), 'Barcode Configurations retrieved successfully');
    }

    /**
     * @param CreateBarcodeConfigurationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/barcodeConfigurations",
     *      summary="Store a newly created BarcodeConfiguration in storage",
     *      tags={"BarcodeConfiguration"},
     *      description="Store BarcodeConfiguration",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BarcodeConfiguration that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BarcodeConfiguration")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BarcodeConfiguration"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBarcodeConfigurationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'barcode_font' => 'required',
            'page_size' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $company = Company::find($input['companySystemID']);

        if (empty($company)) {
            return $this->sendError('Company not found');
        }

        $input['companyID'] = $company->CompanyID;

        //return $this->sendResponse($input, 'Barcode Configuration saved successfully');

        $barcodeConfiguration = $this->barcodeConfigurationRepository->create($input);

        return $this->sendResponse($barcodeConfiguration->toArray(), 'Barcode Configuration saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/barcodeConfigurations/{id}",
     *      summary="Display the specified BarcodeConfiguration",
     *      tags={"BarcodeConfiguration"},
     *      description="Get BarcodeConfiguration",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BarcodeConfiguration",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BarcodeConfiguration"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var BarcodeConfiguration $barcodeConfiguration */
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            return $this->sendError('Barcode Configuration not found');
        }

        return $this->sendResponse($barcodeConfiguration->toArray(), 'Barcode Configuration retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateBarcodeConfigurationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/barcodeConfigurations/{id}",
     *      summary="Update the specified BarcodeConfiguration in storage",
     *      tags={"BarcodeConfiguration"},
     *      description="Update BarcodeConfiguration",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BarcodeConfiguration",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BarcodeConfiguration that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BarcodeConfiguration")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/BarcodeConfiguration"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBarcodeConfigurationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'barcode_font' => 'required',
            'page_size' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        
        /** @var BarcodeConfiguration $barcodeConfiguration */
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            return $this->sendError('Barcode Configuration not found');
        }

        $barcodeConfiguration = $this->barcodeConfigurationRepository->update($input, $id);

        return $this->sendResponse($barcodeConfiguration->toArray(), 'BarcodeConfiguration updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/barcodeConfigurations/{id}",
     *      summary="Remove the specified BarcodeConfiguration from storage",
     *      tags={"BarcodeConfiguration"},
     *      description="Delete BarcodeConfiguration",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BarcodeConfiguration",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var BarcodeConfiguration $barcodeConfiguration */
        $barcodeConfiguration = $this->barcodeConfigurationRepository->findWithoutFail($id);

        if (empty($barcodeConfiguration)) {
            return $this->sendError('Barcode Configuration not found');
        }

        $barcodeConfiguration->delete();

        return $this->sendResponse($id, 'Barcode Configuration deleted successfully');
    }

    public function getBarcodeConfigurationFormData(Request $request)
    {
  
        $selectedCompanyId = $request['companySystemID'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $barCodeFonts = [["value"=>1,"label"=>"Code 128"],["value"=>2,"label"=>"Code 39"]];
        $paper_szie = [["value"=>1,"label"=>"A3"],["value"=>2,"label"=>"A4"],["value"=>3,"label"=>"55mm x 45mm"]];
        $companies = Company::whereIn('companySystemID', $subCompanies)
            ->selectRaw('companySystemID as value,CONCAT(CompanyID, " - " ,CompanyName) as label')
            ->get();

        $output = array(
            'companies' => $companies,
            'fonts' => $barCodeFonts,
            'paper_szie' => $paper_szie,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }


    public function getAllBarCodeConf(Request $request)
    {


        $input = $request->all();
        $selectedCompanyId = isset($input['companyId']) ? $input['companyId'] : 0;
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        
        $barcode = BarcodeConfiguration::withoutGlobalScope(ActiveScope::class)
        ->with(['company'])
            ->orderBy('id', $sort);

        //if (isset($input['isAll']) && !$input['isAll']) {
            $barcode = $barcode->whereIn('companySystemID', $subCompanies);
        //}

        $search = $request->input('search.value');

        

        return \DataTables::of($barcode)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }


    public function genearetBarcode(Request $request) {
        $input = $request->all();

        $selectedCompanyId = $request['companyID'];
        $template = $request['template'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        }
        else {
            $subCompanies = [$selectedCompanyId];
        }

        $configuration = BarcodeConfiguration::where('companySystemID',$subCompanies[0])->first();
        $company = Company::find($subCompanies[0]);
       
        $logo = $company->logo_url && $company->logo_url != null?$company->logo_url:null;
        $companyArabicName = $company->CompanyNameLocalized;
        $type = $request->get('type');

        $pageSizes = array(
            1 => 'A3',
            2 => 'A4',
            3 => 'Custom Size'
        );

        $barCodeFonts = array(
            1 => 'Code 128',
            2 => 'Code 39'
        );

        if(isset($configuration)) {
            if(isset($pageSizes[$configuration->page_size])){
                $page = $pageSizes[$configuration->page_size];
            }
    
            if(isset($barCodeFonts[$configuration->barcode_font])){
                $font = $barCodeFonts[$configuration->barcode_font];
            }

            $pageHeight = 20;
            $pageWidth = 50;

            if($type == 1) {
                $assets = FixedAssetMaster::with('location')->orderBy('faID', 'desc')->ofCompany($subCompanies)->get();
            }
            else {
                $input = $this->convertArrayToValue($input);

                $search = $request->get('search_val');
    
                $assetCositng = FixedAssetMaster::with(['location','category_by', 'sub_category_by', 'finance_category'])->ofCompany($subCompanies);
    
                if (array_key_exists('confirmedYN', $input)) {
                    if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                        $assetCositng->where('confirmedYN', $input['confirmedYN']);
                    }
                }
        
                if (array_key_exists('approved', $input)) {
                    if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                        $assetCositng->where('approved', $input['approved']);
                    }
                }
        
                if (array_key_exists('mainCategory', $input)) {
                    if ($input['mainCategory']) {
                        $assetCositng->where('faCatID', $input['mainCategory']);
                    }
                }
        
                if (array_key_exists('subCategory', $input)) {
                    if ($input['subCategory']) {
                        $assetCositng->where('faSubCatID', $input['subCategory']);
                    }
                }
        
                if (array_key_exists('auditCategory', $input)) {
                    if ($input['auditCategory']) {
                        $assetCositng->where('AUDITCATOGARY', $input['auditCategory']);
                    }
                }
    
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $assetCositng = $assetCositng->where(function ($query) use ($search) {
                        $query->where('faCode', 'LIKE', "%{$search}%")
                            ->orWhere('assetDescription', 'LIKE', "%{$search}%")
                            ->orWhere('docOrigin', 'LIKE', "%{$search}%")
                            ->orWhere('faUnitSerialNo', 'LIKE', "%{$search}%");
                    });
                }

                $assets = $assetCositng->orderBy('faID', 'desc')->get();
            }
       
            $bold = $request['bold'] ? 'B' : '';
            $temp_png = null;
            if($logo != null)
            {
                $imageContent = file_get_contents($logo);
                $fileName = 'companyLogo.jpg';
                $filePath = 'public/images/' . $fileName;
            
                Storage::put($filePath, $imageContent);
                $temp_png = storage_path('app/' . $filePath);
            }
 
               $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
                $barcodesCountPage = 0;
                if($page == "A4") {
                    $maxBarcodesPerPage = 39;
                    $columnSpacing = 6.5; 
                    $marginLeft = 7;
                    $barcodeWidth = 45;
                }
                else if($page == "A3") {
                    $maxBarcodesPerPage = 62;
                    $columnSpacing = 3.8; 
                    $marginLeft = 7;
                    $barcodeWidth = 45;
                }
                else if($page == "Custom Size") {
                    $maxBarcodesPerPage = 1;
                    $columnSpacing = 3.5; 
                    $marginLeft = 4;
                    $barcodeWidth = 41;
                }
               
                $maxBarcodesPerRow = 7; 
                $marginTop = 5;
                $barcodeHeight = 20;
                $rowHeight = 45;
                $row = 0;
                $column = 0;
        
               
                
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);
        
                $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
                $pdf->SetMargins($marginLeft, $marginTop, $marginLeft, true);
                $pdf->SetHeaderMargin(10);
                $pdf->SetFooterMargin(10);
        
                $pdf->SetAutoPageBreak(true, 5);
        
                $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
                $pdf->AddFont('aealarabiya', '', 'aealarabiya.php');
        
                $style = array(
                    'position' => '',
                    'align' => 'C',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => true,
                    'hpadding' => 'auto',
                    'vpadding' => 'auto',
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false,
                    'text' => true,
                    'font' => 'helvetica',
                    'fontsize' => 8,
                    'stretchtext' => 4
                );
            
                    foreach ($assets as $key => $val) {
                        if ($barcodesCountPage % $maxBarcodesPerPage == 0) {
                            if($page == "A4") {
                                $pdf->AddPage('P', 'A4');
                            }
                            else if($page == "A3") {
                                $pdf->AddPage('P', 'A3');
                            }
                            else if($page == "Custom Size") {
                                $pdf->AddPage('P', array(55, 45));
                            }
                            
                            $barcodesCountPage = 0;
                            $row = 0;
                            $column = 0;
                        }
            
                        $x = $marginLeft + ($column * ($barcodeWidth + $columnSpacing));
                        $y = $marginTop + ($row * $rowHeight);
                        if($template == 2)
                        {
                            $imageHeight = 10; 
                            $pdf->Image($temp_png, $x-3, $y, 10, $imageHeight, 'JPG', '', 'T', true, 300, '', false, false, 0, false, false, false);
                            if (file_exists($temp_png)) {
                                if (unlink($temp_png)) {
                                } 
                            };
                            $pdf->SetFont('aealarabiya', '', 8);
                            $pdf->SetXY($x + 7, $y);
                            $pdf->Write(0, $companyArabicName, '', 0, 'L', true, 0, false, false, 0);

                            
                            $pdf->SetXY($x-3, $y + 11);
                            $pdf->Write(0, $val->assetDescription, '', 0, 'L', true, 0, false, false, 0);

                            
                            $pdf->SetFont('helvetica', '', 8);
                            $pdf->SetXY($x + 7, $y + 5);
                            $pdf->Write(0, $val->companyID, '', 0, 'L', true, 0, false, false, 0);

                       }
                       else
                       {
                            $pdf->SetFont('helvetica', '', 8);
                            $pdf->SetXY($x-2, $y+11);
                            $pdf->Write(0, $val->companyID, '', 0, 'L', true, 0, false, false, 0);
                       }
            
                        
                        if($font == 'Code 39') {
                            $pdf->write1DBarcode($val->faCode, 'C39E', $x-2, $y + 15, $barcodeWidth, $barcodeHeight, 0.4, $style, 'N');
                        }
                        else if($font == 'Code 128') {
                            $pdf->write1DBarcode($val->faCode, 'C128', $x-2, $y + 15, $barcodeWidth, $barcodeHeight, 0.4, $style, 'N');
                        }
            
                        $barcodesCountPage++;
                        $column++;
                        if ($column == $maxBarcodesPerRow) {
                            $column = 0;
                            $row++;
                        }
                    }
            
                    $pdf->Output();
 

        }
        else {
            return $this->sendError('Barcode Configuration not found');
        }
    }

    public function checkConfigurationExit(Request $request)
    {
        $input = $request->all();
        $com_id = $input['companyId'];
        $configuration = BarcodeConfiguration::where('companySystemID',$com_id)->first();
        if(isset($configuration))
        {

        return $this->sendResponse(true, 'Record retrieved successfully');
        }
        else
        {
            return $this->sendError('Barcode Configuration not found');
        }

       
    }
}
