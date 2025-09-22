import {useApi} from "../global/useApi";
import {Course} from "../../types/course";
import {CoursesDashboard} from "../../types/courseDashboard";

type PageMeta = { current_page:number; last_page:number; per_page:number; total:number }
type Paged<T> = { data:T[]; meta?:PageMeta; links?:any }

export const useCoursesApi = () => {
    const { get, post, put, del } = useApi()

    const dashboard = () => get<CoursesDashboard>('/courses/dashboard')

    const list = (page=1, perPage=10, filterMsg?:string) =>
        get<Paged<Course>>(
            `/courses?page=${page}&per_page=${perPage}${filterMsg ? `&filterMsg=${encodeURIComponent(filterMsg)}` : ''}`
        )

    const show = (id:number) =>
        get<Course>(`/courses/${id}`)

    const create = (payload: Omit<Course,'id'|'created_at'|'updated_at'>) =>
        post<Course>('/courses', payload)

    const update = (id:number, payload: Partial<Course>) =>
        put<Course>(`/courses/${id}`, payload)

    const destroy = (id:number) =>
        del<{}>(`/courses/${id}`)

    const listStudents = (courseId:number, page=1, perPage=100) =>
        get<Paged<any>>(`/courses/${courseId}/students?page=${page}&per_page=${perPage}`)

    return { dashboard, list, show, create, update, destroy, listStudents }
}
