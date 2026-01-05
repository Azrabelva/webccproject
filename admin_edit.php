/* ================= ADMIN LOCKED STYLE ================= */

:root{
    --pink:#ec4899;
    --pink-dark:#db2777;
    --green:#22c55e;
    --red:#ef4444;
    --purple:#8b5cf6;
    --soft:#fce7f3;
}

*{
    box-sizing:border-box;
    font-family:'Poppins',Arial,sans-serif;
}

/* ================= PAGE ================= */
.page-bg{
    min-height:100vh;
    background:linear-gradient(135deg,#fde2f3,#fbcfe8,#e0e7ff);
}

/* ================= HEADER ================= */
.lc-header{
    background:linear-gradient(135deg,var(--pink),var(--pink-dark));
    padding:16px 0;
    color:#fff;
}

.header-flex{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo-text{
    font-size:20px;
    font-weight:800;
}

.lc-header nav a{
    color:#fff;
    text-decoration:none;
    margin-left:18px;
    font-weight:600;
}

/* ================= ADMIN STATS ================= */
.admin-stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-top:30px;
}

.stat-box{
    padding:24px;
    border-radius:20px;
    color:#fff;
    box-shadow:0 18px 40px rgba(0,0,0,.18);
}

.stat-box h4{
    font-size:14px;
    opacity:.9;
}

.stat-box p{
    font-size:28px;
    font-weight:800;
    margin-top:6px;
}

.stat-box.pink{background:linear-gradient(135deg,#ec4899,#db2777);}
.stat-box.green{background:linear-gradient(135deg,#22c55e,#16a34a);}
.stat-box.red{background:linear-gradient(135deg,#ef4444,#dc2626);}
.stat-box.purple{background:linear-gradient(135deg,#8b5cf6,#7c3aed);}

/* ================= CARD ================= */
.card{
    background:#fff;
    border-radius:22px;
    padding:28px;
    margin-top:32px;
    box-shadow:0 20px 40px rgba(0,0,0,.12);
}

.card h2{
    color:#be185d;
    margin-bottom:18px;
}

/* ================= TEMPLATE GRID ================= */
.admin-template-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:22px;
}

.admin-template-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 12px 30px rgba(0,0,0,.15);
    transition:.25s;
}

.admin-template-card:hover{
    transform:translateY(-6px);
}

.admin-template-card img{
    width:100%;
    height:140px;
    object-fit:cover;
}

.admin-template-info{
    padding:14px;
}

.admin-template-info h4{
    font-size:15px;
    font-weight:700;
    margin-bottom:8px;
}

.badge{
    display:inline-block;
    padding:4px 12px;
    border-radius:20px;
    font-size:11px;
    font-weight:700;
    color:#fff;
}

.badge-free{background:#9ca3af;}
.badge-premium{background:var(--pink);}

/* TEMPLATE ACTION */
.admin-template-action{
    padding:12px;
    border-top:1px solid #eee;
    text-align:center;
}

.btn-edit{
    display:inline-block;
    padding:8px 14px;
    border-radius:12px;
    background:var(--soft);
    color:var(--pink-dark);
    text-decoration:none;
    font-weight:700;
    font-size:13px;
}

.btn-edit:hover{
    background:#fbcfe8;
}

/* ================= TABLE ================= */
.table-wrapper{
    overflow-x:auto;
}

.table{
    width:100%;
    border-collapse:collapse;
}

.table th,
.table td{
    padding:12px;
    border-bottom:1px solid #eee;
    font-size:14px;
}

.table th{
    background:var(--soft);
    color:#be185d;
    text-align:left;
}

.badge-paid{
    background:var(--green);
    color:#fff;
    padding:4px 12px;
    border-radius:14px;
    font-size:12px;
}

.badge-unpaid{
    background:var(--red);
    color:#fff;
    padding:4px 12px;
    border-radius:14px;
    font-size:12px;
}

/* ================= FOOTER ================= */
.lc-footer{
    text-align:center;
    padding:24px;
    font-size:13px;
    color:#555;
}
