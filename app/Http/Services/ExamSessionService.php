<?php

namespace App\Http\Services;

use App\Models\ExamSession;
use Exception;

class ExamSessionService
{
    /**
     * @param $params
     * @return mixed
     */
    public function getExamSessions($params)
    {
        $limit = array_get($params, 'limit', 10);

        return ExamSession::join('modules', 'exam_sessions.module_id','modules.id')
        ->join('test_sites', 'exam_sessions.test_site_id','test_sites.id')
        ->select('modules.name as module_name', 'test_sites.name as test_site_name', 'exam_sessions.started_at', 'exam_sessions.finished_at')
        ->when(!empty(array_get($params, 'search')), function ($query) use ($params) {
            $search = array_get($params, 'search');
            return $query->where('name', 'like', "%$search%");
        })->orderBy('exam_sessions.created_at', 'desc')->paginate($limit);
    }

    /**
     * @param ExamSession $examSession
     * @return ExamSession
     */
    public function getOneExamSession(ExamSession $examSession)
    {
        return $examSession;
    }

    /**
     * @param $params
     * @return ExamSession
     */
    public function storeExamSession($params)
    {
        $examSession = ExamSession::create([
            'module_id' => array_get($params, 'module_id'),
            'test_site_id' => array_get($params, 'test_site_id'),
            'started_at' => array_get($params, 'started_at'),
            'finished_at' => array_get($params, 'finished_at'),
        ]);

        return $this->getOneExamSession($examSession);
    }

    /**
     * @param ExamSession $examSession
     * @param $params
     * @return ExamSession
     */
    public function updateExamSession(ExamSession $examSession, $params)
    {
        $examSession->update([
          'name' => array_get($params, 'name'),
          'capacity' => array_get($params, 'capacity'),
        ]);

        return $examSession;
    }

    /**
     * @param $params
     * @return string
     */
    public function deleteManyExamSessions($params)
    {
        $examSessionIds = array_get($params, 'ids', []);

        if (count($examSessionIds) > 0) {
            ExamSession::whereIn('id', $examSessionIds)->delete();
        }

        return 'ok';
    }

    /**
     * @param ExamSession $examSession
     * @return string
     * @throws Exception
     */
    public function deleteOneExamSession(ExamSession $examSession)
    {
        $examSession->delete();

        return 'ok';
    }
}
