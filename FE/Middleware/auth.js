export default async function ({ store, redirect, route }) {
  // Pastikan user sudah login (dari store atau token di localStorage)
  if (!store.state.auth.loggedIn) {
    return redirect('/login');
  }

  

  // Di sisi client, cek token yang tersimpan di localStorage
  if (process.client) {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      localStorage.removeItem('auth_token'); // Menghapus token jika tidak ada
      return redirect('/login'); // Redirect ke halaman login
    }

    try {
      // Verifikasi token ke API /user untuk mendapatkan data user
      const response = await fetch('http://localhost:8000/api/user', {
        headers: {
          Accept: 'application/json',
          Authorization: `Bearer ${token}`,
        },
      });

      // Jika response tidak OK, buang token dan redirect ke login
      if (!response.ok) {
        localStorage.removeItem('auth_token');
        return redirect('/login');
      }

      // Ambil data user dari response API
      const { user } = await response.json();

      // Prevent infinite redirect
      // Cek jika role user dan halaman yang sedang diakses berbeda
      if (user.role === 'Admin' && route.path !== '/admin/dashboard') {
        return redirect('/admin/dashboard');
      } else if (user.role === 'User' && route.path !== '/user/detail') {
        return redirect('/user/detail');
      }

    } catch (err) {
      // Jika ada error (misalnya token invalid), hapus token dan redirect ke login
      localStorage.removeItem('auth_token');
      return redirect('/login');
    }
  }
}
