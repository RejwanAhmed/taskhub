import type { Organization } from "./organization";
import type { User } from "./user";

export interface Invitation {
  id: number;
  token: string;
  email: string;
  role: string;
  organization_id: number;
  inviter_id: number;
  accepted_at: string | null;
  expires_at: string;
  created_at: string;
  organization: Organization;
  inviter: User;
}