<?php
$strQuery = sprintf("update test_published set start_date='%s',finish_date='%s',time='%s',category_seq=%d,group_list_seq=%d,total_score=%d,test_prog_flg=%d,paper_type=%d,record_view_flg=%d,repeat_flg=%d,test_view_type=%d,deadline_flg=%d,display_flg=%d where test_seq=%d and seq=%d",$strStartDate,$strFinishDate,$time,$intCategorySeq,$intGroupSeq,$intTotalScore,$intTestsProgflg,$intTestsPaperType,$intRecordViewFlg,$intRepeatFlg,$intTestViewType,$intDeadlineFlg,$intDisplayFlg,$intTestsSeq,$intPublishSeq);
?>