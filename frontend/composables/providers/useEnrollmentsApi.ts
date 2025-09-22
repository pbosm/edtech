import {useApi} from "../global/useApi";

export const useEnrollmentsApi = () => {
    const { post, put } = useApi()

    const enroll = (student_id:number, course_id:number, enrollment_date:string) =>
        post('/enrollments', { student_id, course_id, enrollment_date })

    const updateProgress = (enrollment_id:number, progress_percentage:number) =>
        put(`/enrollments/${enrollment_id}/progress`, { progress_percentage })

    return { enroll, updateProgress }
}
