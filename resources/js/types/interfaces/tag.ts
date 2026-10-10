export interface Tag {
    id: number;
    organization_id: number;
    name: string;
    slug: string;
    color: string;
    description: string | null;
}