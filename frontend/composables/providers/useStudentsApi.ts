import {useApi} from "../global/useApi";
import {Student} from "../../types/student";

type Paged<T> = { data:T[]; meta?:any }

export const useStudentsApi = () => {
    const { get, post, put } = useApi()

    const list = (page=1, perPage=10, filterMsg?:string) =>
        get<Paged<Student>>(
            `/students?page=${page}&per_page=${perPage}${filterMsg ? `&filterMsg=${encodeURIComponent(filterMsg)}` : ''}`
        )

    const show = (id:number) =>
        get<Student>(`/students/${id}`)

    const create = (payload: Omit<Student, 'id'|'created_at'|'updated_at'>) =>
        post<Student>('/students', payload)

    const update = (id:number, payload: Partial<Student>) =>
        put<Student>(`/students/${id}`, payload)

    return { list, show, create, update }
}
