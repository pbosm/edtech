import {Student} from "./student";

export interface Course {
    id: number
    name: string
    description?: string | null
    duration_hours: number
    created_at?: string | null
    updated_at?: string | null
    students_count?: number
}

export interface CourseDetail extends Course {
    students?: Student[]
}
