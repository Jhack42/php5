<?php
use App\Models\PreviewFinal;

class CarouselDesignController extends Controller
{
    public function store(Request $request)
    {
        $design = new PreviewFinal($request->all());
        $design->save();
        return response()->json(['success' => true, 'design' => $design]);
    }

    public function getActiveDesign()
    {
        $design = PreviewFinal::getActiveDesign();
        return response()->json([
            'success' => true,
            'design' => $design,
            'css' => $design ? $design->generateCSS() : null
        ]);
    }
}
