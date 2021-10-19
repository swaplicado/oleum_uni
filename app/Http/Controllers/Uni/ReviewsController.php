<?php

namespace App\Http\Controllers\Uni;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Uni\ReviewCfg;
use App\Uni\Review;

class ReviewsController extends Controller
{
    public function storeCourseReviews(Request $request)
    {
        try {
            $lReviewCfgs = ReviewCfg::where('showed_type_id', 2)
                                    ->where('showed_reference_id', $request->id_course)
                                    ->where('is_deleted', false)
                                    ->get();

            \DB::beginTransaction();

            foreach ($lReviewCfgs as $oRevCfg) {
                $rate = "rate".$oRevCfg->id_configuration;
                $comments = 'textArea'.$oRevCfg->id_configuration;
    
                $oReview = new Review();
                $oReview->stars = $request->$rate > 0 ? $request->$rate : 0;
                $oReview->review_n_comments =  $request->$rate > 0 ? null : $request->$comments;
                $oReview->is_deleted = false;
                $oReview->review_type_id = $oRevCfg->review_type_id;
                $oReview->reference_id = $oRevCfg->reference_id;
                $oReview->showed_type_id = $oRevCfg->showed_type_id;
                $oReview->showed_reference_id = $oRevCfg->showed_reference_id;
                $oReview->student_by_id = \Auth::id();
    
                $oReview->save();
            }

            \DB::commit();

        }
        catch (\Throwable $th) {
            \DB::rollBack();
            return back()->withError($th->getMessage());
        }

        return redirect()->back();
    }
}
