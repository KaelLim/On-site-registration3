<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>註冊表格</title>
    <link href="styles.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
</head>

<body>
  <h1>11/04 彰化場共修<br>現場志工練習帳號申請系統</h1>
    <form id="registrationForm" action="upload.php" method="post">
        <div class="input-field">
            <input type="text" id="name" name="name" required>
            <label for="name">姓名</label>
        </div>
        <div class="input-field">
            <input type="text" id="phone" name="phone" pattern="09[0-9]{8}" required>
            <label for="phone">手機號碼</label>
        </div>
        <button class="btn waves-effect waves-light" type="button" onclick="showConfirmModal()">註冊 / 帳號查詢</button>
    </form>

    <div id="loadingModal">
        <div class="modal-content">
            <div class="modal-box">
                <div class="modal-message">正在處理...</div>
            </div>
        </div>
    </div>

    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <div id="confirmContent">
                <h5>以下是您填寫的資訊，請核對</h5>
                <p>姓名: <span id="confirmName"></span></p>
                <p>手機號碼: <span id="confirmPhone"></span></p>

                <div class="credentials">
                    <p>您的帳密如下：</p>
                    <p>帳號: <span id="confirmUsername"></span></p>
                    <p>密碼: <span id="confirmPassword"></span></p>
                </div>

                <div class="line-instruction">
                    <p>點擊確認後務必加line官方好友，後續教學會使用line官方</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn waves-effect waves-light red lighten-2" onclick="hideConfirmModal();">返回</button> <!-- 修改這裡 -->
                <button class="btn waves-effect waves-light" onclick="submitForm()">確認</button> <!-- 修改這裡 -->
            </div>
        </div>
    </div>
    <div id="duplicateModal" class="modal">
        <div class="modal-content">
            <h5>帳號查詢</h5>
            <p>此名稱和電話號碼已註冊！</p>
            <div class="modal-footer">
                <button class="btn waves-effect waves-light" onclick="hideDuplicateModal()">關閉</button>
            </div>
        </div>
    </div>
    <div id="safariModal" class="modal">
        <div class="modal-content">
            <h5>完成註冊!</h5>
            <p>請點擊以下連結加入 LINE 官方好友:</p>
            <a class="line-button" href="https://line.me/R/ti/p/@052bywpq" target="_blank">官方好友</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalElems = document.querySelectorAll('.modal');
            var modalInstances = M.Modal.init(modalElems, {});
        });
      
        function isMobileDevice() {
            return (typeof window.orientation !== "undefined") || (navigator.userAgent.indexOf('IEMobile') !== -1);
        }
      
        function showConfirmModal() {
            if (!validateName()){
                return;
            }
            const name = $("#name").val().trim();
            const phone = $("#phone").val();
      
            if (!name) {
                alert("請填寫姓名。");
                return;
            }
      
            const phonePattern = /^09[0-9]{8}$/;
            if (!phonePattern.test(phone)) {
                alert("請正確填寫手機號碼，必須是09開始，總數10碼(含09)。");
                return;
            }
      
            $.post("check_duplicate.php", { name: name, phone: phone }, function(response) {
            if (response.isDuplicate) {
                showDuplicateModal();
            } else {
                $("#confirmName").text(name);
                $("#confirmPhone").text(phone);
                $("#confirmUsername").text("tc" + phone);
                $("#confirmPassword").text("tc" + phone);

                var modalElem = $('#confirmModal');
                var modalInstance = M.Modal.getInstance(modalElem);
                modalInstance.open();
                }
            }, "json");
        }
      
        function showDuplicateModal() {
            var modalElem = $('#duplicateModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.open();
        }
      
        function hideDuplicateModal() {
            var modalElem = $('#duplicateModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.close();
        }
      
        function hideConfirmModal() {
            var modalElem = $('#confirmModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.close();
        }
      
        function submitForm() {
            hideConfirmModal();
            $("#registrationForm").submit();
      
            $("#loadingModal").css("display", "flex");
            $("button").prop("disabled", true);
      
            const name = $("#name").val().trim();
            const phone = $("#phone").val();
            const ldapAccount = "tc" + phone;
      
            // 以下是模擬的 api 請求
            setTimeout(function() {
                const response = {
                    result: "success" // 範例值
                };
      
                $("#loadingModal").hide();
                $("button").prop("disabled", false);
      
                if (response.result === "success") {
                    if (isMobileDevice()) {
                        window.location.href = "line://ti/p/@052bywpq";
                        showSafariModal();
                    } else {
                        window.open("https://line.me/R/ti/p/@052bywpq", '_self');
                    }
                } else {
                    alert("註冊失敗，請再試一次!");
                }
            }, 1000);
        }
      
        function showSafariModal() {
            var modalElem = $('#safariModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.open();
        }
      
        function hideSafariModal() {
            var modalElem = $('#safariModal');
            var modalInstance = M.Modal.getInstance(modalElem);
            modalInstance.close();
        }

        function validateName() {
            var nameInput = document.getElementById('name');
            var name = nameInput.value;
        
            // 正則表達式檢查名字是否只包含字母和空格
            if (!/^[a-zA-Z\s]+$/.test(name)) {
                alert('名字只能包含字母和空格。');
                return false;
            }
            return true;
        }



      </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>

</html>
~                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     
~                                                
