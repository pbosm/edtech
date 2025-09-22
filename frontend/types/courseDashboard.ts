export interface CoursesDashboard {
    totals: {
        courses: number
        students: number
        avg_duration_hours: number
    }
    chart: { labels: string[]; values: number[] }
    top_courses: Array<{ id:number; name:string; duration_hours:number; students_count:number }>
}