const API_URL = "/api/products";

// โหลดข้อมูลเมื่อเปิดหน้าเว็บ
document.addEventListener("DOMContentLoaded", function () {
    loadProducts();
});


// ========================================
// 1. โหลดสินค้าทั้งหมด
// ========================================
async function loadProducts() {

    try {

        const response = await fetch(API_URL);

        if (!response.ok) {
            throw new Error("API Error: " + response.status);
        }

        const result = await response.json();

        if (!result.success) {
            alert("ไม่สามารถโหลดข้อมูลสินค้าได้");
            return;
        }

        displayProducts(result.data);

    } catch (error) {

        console.error("Load Error:", error);
        alert("ไม่สามารถเชื่อมต่อ API ได้");

    }
}


// ========================================
// 2. แสดงสินค้า
// ========================================
function displayProducts(products) {

    const table = document.getElementById("productTable");

    table.innerHTML = "";

    if (!products || products.length === 0) {

        const row = document.createElement("tr");
        const cell = document.createElement("td");

        cell.colSpan = 7;
        cell.textContent = "ไม่พบข้อมูลสินค้า";

        row.appendChild(cell);
        table.appendChild(row);

        return;
    }


    products.forEach(function (product) {

        const row = document.createElement("tr");


        // ID
        const idCell = document.createElement("td");
        idCell.textContent = product.i_ProductID;
        row.appendChild(idCell);


        // ชื่อสินค้า
        const nameCell = document.createElement("td");
        nameCell.textContent = product.c_ProductName;
        row.appendChild(nameCell);


        // Supplier ID
        const supplierCell = document.createElement("td");
        supplierCell.textContent = product.i_SupplierID;
        row.appendChild(supplierCell);


        // Category ID
        const categoryCell = document.createElement("td");
        categoryCell.textContent = product.i_CategoryID;
        row.appendChild(categoryCell);


        // หน่วย
        const unitCell = document.createElement("td");
        unitCell.textContent = product.c_Unit;
        row.appendChild(unitCell);


        // ราคา
        const priceCell = document.createElement("td");
        priceCell.textContent = Number(product.i_Price).toFixed(2);
        row.appendChild(priceCell);


        // จัดการ
        const actionCell = document.createElement("td");


        // ปุ่มแก้ไข
        const editButton = document.createElement("button");

        editButton.textContent = "แก้ไข";
        editButton.className = "edit-btn";

        editButton.onclick = function () {
            editProduct(product);
        };


        // ปุ่มลบ
        const deleteButton = document.createElement("button");

        deleteButton.textContent = "ลบ";
        deleteButton.className = "delete-btn";

        deleteButton.onclick = function () {
            deleteProduct(product.i_ProductID);
        };


        actionCell.appendChild(editButton);
        actionCell.appendChild(deleteButton);

        row.appendChild(actionCell);

        table.appendChild(row);

    });
}


// ========================================
// 3. ค้นหาสินค้า
// ========================================
async function searchProducts() {

    const searchInput =
        document.getElementById("searchInput");

    const search =
        searchInput.value.trim();


    if (search === "") {

        loadProducts();

        return;
    }


    try {

        const response = await fetch(
            API_URL + "?search=" + encodeURIComponent(search)
        );


        if (!response.ok) {
            throw new Error("API Error: " + response.status);
        }


        const result = await response.json();


        if (!result.success) {

            alert("ไม่สามารถค้นหาสินค้าได้");

            return;
        }


        displayProducts(result.data);


    } catch (error) {

        console.error("Search Error:", error);

        alert("ไม่สามารถค้นหาข้อมูลได้");

    }

}


// ========================================
// 4. เปิดฟอร์มเพิ่มสินค้า
// ========================================
function openAddForm() {

    document.getElementById("productForm").style.display = "block";

    document.getElementById("formTitle").textContent =
        "เพิ่มสินค้า";

    document.getElementById("productId").value = "";

    document.getElementById("productName").value = "";

    document.getElementById("supplierId").value = "";

    document.getElementById("categoryId").value = "";

    document.getElementById("unit").value = "";

    document.getElementById("price").value = "";

}


// ========================================
// 5. ปิดฟอร์ม
// ========================================
function closeForm() {

    document.getElementById("productForm").style.display =
        "none";

}


// ========================================
// 6. บันทึกสินค้า
// ========================================
async function saveProduct() {

    const productId =
        document.getElementById("productId").value;

    const productName =
        document.getElementById("productName").value.trim();

    const supplierId =
        document.getElementById("supplierId").value;

    const categoryId =
        document.getElementById("categoryId").value;

    const unit =
        document.getElementById("unit").value.trim();

    const price =
        document.getElementById("price").value;


    // Validation

    if (productName === "") {

        alert("กรุณากรอกชื่อสินค้า");

        return;
    }


    if (supplierId === "") {

        alert("กรุณากรอก Supplier ID");

        return;
    }


    if (categoryId === "") {

        alert("กรุณากรอก Category ID");

        return;
    }


    if (unit === "") {

        alert("กรุณากรอกหน่วยสินค้า");

        return;
    }


    if (price === "") {

        alert("กรุณากรอกราคา");

        return;
    }


    if (Number(price) < 0) {

        alert("ราคาต้องไม่ติดลบ");

        return;
    }


    const data = {

        c_ProductName: productName,

        i_SupplierID: Number(supplierId),

        i_CategoryID: Number(categoryId),

        c_Unit: unit,

        i_Price: Number(price)

    };


    try {

        let response;


        // ========================================
        // แก้ไขสินค้า
        // ========================================
        if (productId !== "") {

            response = await fetch(

                API_URL + "/" + productId,

                {

                    method: "PUT",

                    headers: {

                        "Content-Type":
                            "application/json"

                    },

                    body: JSON.stringify(data)

                }

            );

        }


        // ========================================
        // เพิ่มสินค้า
        // ========================================
        else {

            response = await fetch(

                API_URL,

                {

                    method: "POST",

                    headers: {

                        "Content-Type":
                            "application/json"

                    },

                    body: JSON.stringify(data)

                }

            );

        }


        if (!response.ok) {

            throw new Error(
                "API Error: " + response.status
            );

        }


        const result =
            await response.json();


        if (result.success) {

            if (productId !== "") {

                alert(
                    "แก้ไขข้อมูลสินค้าเรียบร้อยแล้ว"
                );

            } else {

                alert(
                    "เพิ่มข้อมูลสินค้าเรียบร้อยแล้ว"
                );

            }


            closeForm();

            loadProducts();


        } else {

            alert(
                result.message ||
                "ไม่สามารถบันทึกข้อมูลได้"
            );

        }


    } catch (error) {

        console.error(
            "Save Error:",
            error
        );

        alert(
            "ไม่สามารถบันทึกข้อมูลได้"
        );

    }

}


// ========================================
// 7. แก้ไขสินค้า
// ========================================
function editProduct(product) {

    document.getElementById("productForm").style.display =
        "block";


    document.getElementById("formTitle").textContent =
        "แก้ไขสินค้า";


    document.getElementById("productId").value =
        product.i_ProductID;


    document.getElementById("productName").value =
        product.c_ProductName;


    document.getElementById("supplierId").value =
        product.i_SupplierID;


    document.getElementById("categoryId").value =
        product.i_CategoryID;


    document.getElementById("unit").value =
        product.c_Unit;


    document.getElementById("price").value =
        product.i_Price;

}


// ========================================
// 8. ลบสินค้า
// ========================================
async function deleteProduct(id) {

    const confirmDelete = confirm(
        "คุณต้องการลบสินค้านี้ใช่หรือไม่?"
    );


    if (!confirmDelete) {

        return;
    }


    try {

        const response = await fetch(

            API_URL + "/" + id,

            {

                method: "DELETE"

            }

        );


        if (!response.ok) {

            throw new Error(
                "API Error: " + response.status
            );

        }


        const result =
            await response.json();


        if (result.success) {

            alert(
                "ลบข้อมูลสินค้าเรียบร้อยแล้ว"
            );

            loadProducts();


        } else {

            alert(
                result.message ||
                "ไม่สามารถลบข้อมูลได้"
            );

        }


    } catch (error) {

        console.error(
            "Delete Error:",
            error
        );

        alert(
            "ไม่สามารถลบข้อมูลได้"
        );

    }

}

