import DashboardHeader from "../_components/dashboard-header";
import UserManagementContent from "./_components/user-management-content";

export default  function UserManagementPage() {
  return (
    <section>
      <DashboardHeader
        title="User"
        description="Kelola semua akun user yang terdata di aplikasi"
      />
      <div className="p-4">
        <UserManagementContent />
      </div>
    </section>
  );
}
