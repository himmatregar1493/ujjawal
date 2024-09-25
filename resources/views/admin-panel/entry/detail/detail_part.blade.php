<style>
    #fetched-data {
        background: aliceblue;
        padding: 10px;
        border-radius: 10px;
    }

    .vendor-detail {
        display: flex;
        align-items: center; /* Vertically center items */
        margin-bottom: 10px; /* Space between rows */
    }

    .vendor-title {
        font-weight: bold;   /* Bold titles */
        width: 150px;        /* Fixed width for title */
    }

    .vendor-separator {
        width: 10px;         /* Fixed width for separator */
        text-align: center;  /* Center separator */
    }

    .vendor-value {
        flex: 1;             /* Takes remaining space */
        text-align: left;    /* Align text to the left */
        color: #333;
        text-indent: 20px;       /* Adjust color as needed */
    }
</style>
<div id="" class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
    <div class="col">
        <div class="vendor-detail">
            <span class="vendor-title">Name</span>
            <span class="vendor-separator">:</span>
            <span class="vendor-value">{{ $data['name'] ?? '-' }}</span>
        </div>
    </div>
    <div class="col">
        <div class="vendor-detail">
            <span class="vendor-title">Email</span>
            <span class="vendor-separator">:</span>
            <span class="vendor-value">{{ $data['email'] ?? '-' }}</span>
        </div>
    </div>
    <div class="col">
        <div class="vendor-detail">
            <span class="vendor-title">Contact</span>
            <span class="vendor-separator">:</span>
            <span class="vendor-value">{{ $data['phone'] ?? '-' }}</span>
        </div>
    </div>
    <div class="col">
        <div class="vendor-detail">
            <span class="vendor-title">Alternate Contact</span>
            <span class="vendor-separator">:</span>
            <span class="vendor-value">{{ $data['alt_phone'] ?? '-' }}</span>
        </div>
    </div>
    <div class="col">
        <div class="vendor-detail">
            <span class="vendor-title">Address</span>
            <span class="vendor-separator">:</span>
            <span class="vendor-value">{{ $data['address'] ?? '-' }}</span>
        </div>
    </div>
</div>
